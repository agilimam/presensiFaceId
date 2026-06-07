import mysql.connector
import face_recognition
import os
import time

# KONFIGURASI PATH (Sesuaikan dengan folder storage kamu)
BASE_DIR = r"D:\laragon\www\presensiFaceId"
PATH_FACES = os.path.join(BASE_DIR, "storage/app/public/faces")

def get_db():
    return mysql.connector.connect(
        host="127.0.0.1", 
        user="root", 
        password="", 
        database="masjid_aliman"
    )

def cek_duplikasi():
    db = get_db()
    cursor = db.cursor(dictionary=True, buffered=True)

    cursor.execute("SELECT id_anggota_keluarga, face_id FROM anggota_keluarga WHERE status_wajah = 'PENDING'")
    pendings = cursor.fetchall()

    for p in pendings:
        print(f"🔄 Memproses verifikasi ID {p['id_anggota_keluarga']}...")
        
        path_baru = os.path.join(PATH_FACES, p['face_id'])
        
        # 1. Cek file ada atau tidak
        if not os.path.exists(path_baru):
            cursor.execute("UPDATE anggota_keluarga SET status_wajah = 'GAGAL' WHERE id_anggota_keluarga = %s", (p['id_anggota_keluarga'],))
            db.commit()
            continue
        
        # 2. Coba proses loading wajah
        try:
            img_baru = face_recognition.load_image_file(path_baru)
            enc_baru = face_recognition.face_encodings(img_baru)

            if not enc_baru:
                raise Exception("Wajah tidak terdeteksi")
        except Exception as e:
            print(f"❌ Gagal membaca wajah: {e}")
            if os.path.exists(path_baru):
                os.remove(path_baru)
            cursor.execute("UPDATE anggota_keluarga SET face_id = NULL, status_wajah = 'GAGAL' WHERE id_anggota_keluarga = %s", (p['id_anggota_keluarga'],))
            db.commit()
            continue

        # 3. Bandingkan dengan yang sudah VERIFIED
        cursor.execute("SELECT id_anggota_keluarga, face_id FROM anggota_keluarga WHERE status_wajah = 'VERIFIED' AND id_anggota_keluarga != %s", (p['id_anggota_keluarga'],))
        existings = cursor.fetchall()
        
        is_duplicate = False
        for e in existings:
            path_exist = os.path.join(PATH_FACES, e['face_id'])
            if not os.path.exists(path_exist): continue
            
            img_exist = face_recognition.load_image_file(path_exist)
            enc_exist = face_recognition.face_encodings(img_exist)
            
            if enc_exist:
                match = face_recognition.compare_faces([enc_exist[0]], enc_baru[0], tolerance=0.45)
                if match[0]:
                    print(f"⚠️ Ditemukan duplikat dengan ID {e['id_anggota_keluarga']}")
                    is_duplicate = True
                    break
        
        # 4. Update Status (Berada di dalam loop for p in pendings)
        if is_duplicate:
            print(f"⚠️ Wajah duplikat, menghapus file {p['face_id']}")
            if os.path.exists(path_baru):
                os.remove(path_baru)
            cursor.execute("UPDATE anggota_keluarga SET face_id = NULL, status_wajah = 'DUPLICATE' WHERE id_anggota_keluarga = %s", (p['id_anggota_keluarga'],))
        else:
            final_filename = f"face_{p['id_anggota_keluarga']}.jpg"
            final_path = os.path.join(PATH_FACES, final_filename)
            if os.path.exists(final_path): os.remove(final_path)
            os.rename(path_baru, final_path)
            cursor.execute("UPDATE anggota_keluarga SET face_id = %s, status_wajah = 'VERIFIED' WHERE id_anggota_keluarga = %s", (final_filename, p['id_anggota_keluarga']))
        
        db.commit()
        print(f"✅ Selesai: {p['id_anggota_keluarga']} => {'DUPLICATE' if is_duplicate else 'VERIFIED'}")

    cursor.close()
    db.close()

if __name__ == "__main__":
    print("🚀 AI Register Aktif! Memantau pendaftaran wajah baru...")
    while True:
        try:
            cek_duplikasi()
        except Exception as e:
            print(f"Error: {e}")
        time.sleep(2)