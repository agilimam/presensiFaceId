import mysql.connector
import face_recognition
import os
import time
import numpy as np

# 1. KONFIGURASI DATABASE
def get_db_connection():
    return mysql.connector.connect(
        host="127.0.0.1",
        user="root",
        password="",
        database="masjid_aliman" 
    )

db = get_db_connection()
# Buffered agar cursor tidak tabrakan saat update
cursor = db.cursor(dictionary=True, buffered=True)

PATH_FACES = "storage/app/public/faces/"
PATH_SCAN = "storage/app/public/scan_masuk/"

def proses_identifikasi_wajah():
    try:
        db.commit() 
        # PERBAIKAN: Ambil kolom 'id_keluarga' dari antrean presensi Laravel
        cursor.execute("SELECT id_presensi, id_keluarga, face_id FROM presensi WHERE id_anggota_keluarga IS NULL AND face_id != 'GAGAL'")
        antrean_presensi = cursor.fetchall()

        if not antrean_presensi:
            return

        print(f"🔍 Menemukan {len(antrean_presensi)} antrean baru. Memproses...")

        for log in antrean_presensi:
            current_log_id = int(log['id_presensi'])
            id_keluarga_absen = int(log['id_keluarga']) # <-- ID Keluarga dari user yang sedang login
            path_foto_scan = os.path.join(PATH_SCAN, log['face_id'])
            
            if not os.path.exists(path_foto_scan):
                print(f"⚠️ Berkas scan tidak ditemukan: {path_foto_scan}")
                continue

            try:
                # Load foto hasil scan kamera jamaah
                image_scan = face_recognition.load_image_file(path_foto_scan)
                encoding_scan = face_recognition.face_encodings(image_scan)

                if encoding_scan:
                    # KUNCI LOGIKA: Ambil data wajah referensi HANYA untuk keluarga ini saja
                    cursor.execute("""
                        SELECT id_anggota_keluarga, face_id 
                        FROM anggota_keluarga 
                        WHERE status_wajah = 'VERIFIED' AND id_keluarga = %s
                    """, (id_keluarga_absen,))
                    anggota_keluarga_list = cursor.fetchall()

                    is_match_found = False
                    family_encodings = []
                    family_member_ids = []

                    # Daftarkan encoding wajah referensi khusus internal keluarga terkait
                    for member in anggota_keluarga_list:
                        if member['face_id']:
                            path_foto_member = os.path.join(PATH_FACES, member['face_id'])
                            if os.path.exists(path_foto_member):
                                try:
                                    img_member = face_recognition.load_image_file(path_foto_member)
                                    enc_member = face_recognition.face_encodings(img_member)
                                    if enc_member:
                                        family_encodings.append(enc_member[0])
                                        family_member_ids.append(int(member['id_anggota_keluarga']))
                                except Exception as e:
                                    print(f"⚠️ Gagal membaca wajah referensi {member['face_id']}: {e}")

                    # Mulai perbandingan jika keluarga ini memiliki anggota yang sudah VERIFIED
                    if family_encodings:
                        matches = face_recognition.compare_faces(family_encodings, encoding_scan[0], tolerance=0.45)
                        face_distances = face_recognition.face_distance(family_encodings, encoding_scan[0])
                        
                        if len(face_distances) > 0:
                            best_match_index = np.argmin(face_distances)
                            if matches[best_match_index]:
                                id_ketemu = family_member_ids[best_match_index]
                                
                                # Simpan hasil COCOK ke database
                                cursor.execute("UPDATE presensi SET id_anggota_keluarga = %s WHERE id_presensi = %s", (id_ketemu, current_log_id))
                                db.commit()
                                print(f"✅ BERHASIL: ID Anggota {id_ketemu} teridentifikasi di Keluarga {id_keluarga_absen}")
                                is_match_found = True

                    # Jika tidak cocok dengan satu pun anggota di dalam keluarga tersebut
                    if not is_match_found:
                        cursor.execute("UPDATE presensi SET face_id = 'GAGAL' WHERE id_presensi = %s", (current_log_id,))
                        db.commit()
                        print(f"❌ TIDAK COCOK: Wajah asing atau bukan bagian dari Keluarga {id_keluarga_absen}")
                else:
                    cursor.execute("UPDATE presensi SET face_id = 'GAGAL' WHERE id_presensi = %s", (current_log_id,))
                    db.commit()
                    print(f"⚠️ WAJAH TIDAK TERDETEKSI PADA FOTO SCAN")
            
            except Exception as e:
                print(f"⚠️ Error saat memproses log ID {current_log_id}: {e}")

    except mysql.connector.Error as err:
        print(f"❌ Database Error: {err}")
        db.reconnect()
            
if __name__ == "__main__":
    print("🚀 AI Presensi Aktif! Menunggu data baru berbasis penguncian keluarga...")
    while True:
        proses_identifikasi_wajah()
        time.sleep(2)