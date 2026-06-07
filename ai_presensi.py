import mysql.connector
import face_recognition
import os
import time
import numpy as np
import re

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

dataset_encodings = []
dataset_ids = []

def load_dataset():
    global dataset_encodings, dataset_ids
    dataset_encodings = []
    dataset_ids = []
    
    if not os.path.exists(PATH_FACES):
        print(f"❌ Folder dataset tidak ditemukan: {PATH_FACES}")
        return

    print("🔄 Memperbarui dataset wajah referensi...")
    for file_name in os.listdir(PATH_FACES):
        if file_name.lower().endswith((".png", ".jpg", ".jpeg")):
            path_foto = os.path.join(PATH_FACES, file_name)
            try:
                raw_id = file_name.split('.')[0]
                clean_id = re.findall(r'\d+', raw_id)
                
                if clean_id:
                    actual_id = int(clean_id[-1])
                    image = face_recognition.load_image_file(path_foto)
                    encoding = face_recognition.face_encodings(image)
                    
                    if encoding:
                        dataset_encodings.append(encoding[0])
                        dataset_ids.append(actual_id)
                
            except Exception as e:
                print(f"⚠️ Gagal membaca {file_name}: {e}")

def proses_identifikasi_wajah():
    try:
        db.commit() 
        cursor.execute("SELECT id_presensi, face_id FROM presensi WHERE id_anggota_keluarga IS NULL AND face_id != 'GAGAL'")
        antrean_presensi = cursor.fetchall()

        if not antrean_presensi:
            return

        print(f"🔍 Menemukan {len(antrean_presensi)} antrean baru. Memproses...")

        for log in antrean_presensi:
            current_log_id = int(log['id_presensi'])
            path_foto_scan = os.path.join(PATH_SCAN, log['face_id'])
            
            if not os.path.exists(path_foto_scan):
                continue

            try:
                image_scan = face_recognition.load_image_file(path_foto_scan)
                encoding_scan = face_recognition.face_encodings(image_scan)

                if encoding_scan:
                    matches = face_recognition.compare_faces(dataset_encodings, encoding_scan[0], tolerance=0.45)
                    face_distances = face_recognition.face_distance(dataset_encodings, encoding_scan[0])
                    
                    if len(face_distances) > 0:
                        best_match_index = np.argmin(face_distances)
                        if matches[best_match_index]:
                            id_ketemu = int(dataset_ids[best_match_index])
                            cursor.execute("UPDATE presensi SET id_anggota_keluarga = %s WHERE id_presensi = %s", (id_ketemu, current_log_id))
                            db.commit()
                            print(f"✅ BERHASIL: ID {id_ketemu}")
                        else:
                            cursor.execute("UPDATE presensi SET face_id = 'GAGAL' WHERE id_presensi = %s", (current_log_id,))
                            db.commit()
                            print(f"❌ TIDAK COCOK")
                else:
                    cursor.execute("UPDATE presensi SET face_id = 'GAGAL' WHERE id_presensi = %s", (current_log_id,))
                    db.commit()
                    print(f"⚠️ WAJAH TIDAK TERDETEKSI")
            
            except Exception as e:
                print(f"⚠️ Error: {e}")

    except mysql.connector.Error as err:
        print(f"❌ Database Error: {err}")
        db.reconnect()
            
if __name__ == "__main__":
    load_dataset()
    print("🚀 AI Presensi Aktif! Menunggu data baru...")
    while True:
        proses_identifikasi_wajah()
        time.sleep(2)