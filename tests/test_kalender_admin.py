import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
from selenium.webdriver.chrome.service import Service as ChromeService
from webdriver_manager.chrome import ChromeDriverManager

# ===================================================================
# KONFIGURASI
# ===================================================================
BASE_URL = "http://127.0.0.1:8000"
ADMIN_URL = BASE_URL + "/admin"
KALENDER_URL = BASE_URL + "/kalender"

# ===================================================================
# PYTEST FIXTURE (SETUP, LOGIN, HANDLE POP-UP & TEARDOWN)
# ===================================================================
@pytest.fixture
def driver():
    """
    Fixture untuk setup WebDriver, login, menangani notifikasi pop-up, 
    dan teardown (menutup browser) setelah tes selesai.
    """
    # Setup WebDriver
    driver = webdriver.Chrome(service=ChromeService(ChromeDriverManager().install()))
    driver.maximize_window()
    wait = WebDriverWait(driver, 10) 

    # 1. Proses Login Otomatis
    driver.get(ADMIN_URL)
    wait.until(EC.visibility_of_element_located((By.ID, "name"))).send_keys("SuperAdmin")
    driver.find_element(By.ID, "password").send_keys("admin123")
    driver.find_element(By.XPATH, "//button[contains(text(), 'Masuk Sebagai Admin')]").click()
    wait.until(EC.url_contains("/dashboard"))

    # 2. Arahkan ke Halaman Kalender
    driver.get(KALENDER_URL)
    wait.until(EC.visibility_of_element_located((By.ID, "formKegiatan")))
    
    # 3. Penanganan Notifikasi Tambahan (jika muncul)
    try:
        # Waktu tunggu singkat (3 detik) untuk mengecek notifikasi.
        short_wait = WebDriverWait(driver, 3)
        
        # Locator ini mencari tombol di dalam pop-up yang mungkin muncul.
        # Anda mungkin perlu menyesuaikannya berdasarkan inspect element.
        close_button = short_wait.until(
            EC.element_to_be_clickable((By.XPATH, "//*[contains(@class, 'swal2-actions')]//button[contains(text(), 'Tutup') or contains(text(), 'Nanti') or contains(text(), 'Lewati')]"))
        )
        
        print("\nInfo: Notifikasi tambahan terdeteksi, mencoba menutupnya...")
        close_button.click()
        
        # Tunggu sampai pop-up benar-benar hilang dari layar
        wait.until(EC.invisibility_of_element_located((By.CLASS_NAME, "swal2-container")))
        print("Info: Notifikasi berhasil ditutup.")

    except TimeoutException:
        # Jika notifikasi tidak ditemukan, itu bagus, tes bisa lanjut.
        print("\nInfo: Tidak ada notifikasi tambahan terdeteksi.")

    # 4. Serahkan driver yang sudah siap ke fungsi tes
    yield driver

    # 5. Teardown (berjalan setelah tes selesai)
    driver.quit()

# ===================================================================
# KUMPULAN TEST CASE
# ===================================================================

def test_create_valid_activity(driver):
    """
    TC.KLN.001.002: Memverifikasi pembuatan kegiatan baru dengan data yang valid.
    Fokus verifikasi pada hasil akhir (data muncul di kalender).
    """
    wait = WebDriverWait(driver, 10)
    
    # 1. Mengisi form kegiatan
    driver.find_element(By.ID, "judulKegiatan").send_keys("Rapat Koordinasi Bulanan")
    driver.find_element(By.ID, "deskripsiKegiatan").send_keys("Membahas pencapaian dan rencana bulan depan.")
    driver.find_element(By.ID, "tanggalKegiatan").send_keys("2025-06-20")
    
    # 2. Memastikan tombol publikasi aktif
    publikasi_button = wait.until(EC.element_to_be_clickable((By.ID, "btnPublikasikan")))
    assert publikasi_button.is_enabled(), "Tombol Publikasikan seharusnya aktif setelah semua field valid diisi."
    
    # 3. Klik tombol publikasi
    publikasi_button.click()
    
    # 4. VERIFIKASI UTAMA DAN FINAL
    # Setelah klik, langsung tunggu dan verifikasi bahwa event baru muncul di kalender.
    # Ini adalah bukti kesuksesan yang paling andal, bukan notifikasi sementara.
    try:
        new_event_on_calendar = wait.until(
            EC.visibility_of_element_located((By.XPATH, "//*[contains(text(), 'Rapat Koordinasi Bulanan')]"))
        )
        
        # Jika elemen di atas ditemukan, berarti tes berhasil.
        assert new_event_on_calendar.is_displayed()
        
    except TimeoutException:
        # Jika setelah 10 detik event tidak muncul di kalender, baru kita anggap gagal.
        pytest.fail("Event 'Rapat Koordinasi Bulanan' tidak muncul di kalender setelah form disubmit.")

def test_past_date_validation(driver):
    """
    TC.KLN.002.003: Memverifikasi validasi saat user memilih tanggal di masa lalu.
    """
    wait = WebDriverWait(driver, 10)
    driver.find_element(By.ID, "judulKegiatan").send_keys("Kegiatan Test Tanggal Lalu")
    driver.find_element(By.ID, "deskripsiKegiatan").send_keys("Deskripsi untuk kegiatan test tanggal lalu.")
    driver.find_element(By.ID, "tanggalKegiatan").send_keys("01-01-2023")
    
    error_message = wait.until(EC.visibility_of_element_located((By.ID, "tanggalError")))
    assert error_message.is_displayed(), "Pesan error untuk tanggal lalu seharusnya muncul."
    assert "tidak boleh sebelum hari ini" in error_message.text, "Teks pesan error tidak sesuai."
    
    publikasi_button = driver.find_element(By.ID, "btnPublikasikan")
    assert not publikasi_button.is_enabled(), "Tombol Publikasikan seharusnya nonaktif saat tanggal tidak valid."

def test_modal_interaction(driver):
    """
    TS.KLN.003: Memverifikasi interaksi dengan modal detail kegiatan di kalender.
    """
    wait = WebDriverWait(driver, 10)
    try:
        # Asumsi: sudah ada event "Ulang Tahun Bani" di kalender untuk diklik
        event_item = wait.until(
            EC.element_to_be_clickable((By.XPATH, "//*[contains(text(), 'Ulang Tahun Bani')]"))
        )
    except TimeoutException:
        pytest.skip("Test dilewati: Event 'Ulang Tahun Bani' tidak ditemukan di kalender.")
    
    expected_judul = event_item.get_attribute("data-judul")
    expected_deskripsi = event_item.get_attribute("data-deskripsi")
    
    event_item.click()
    
    modal_content = wait.until(EC.visibility_of_element_located((By.ID, "modalContent")))
    assert modal_content.is_displayed(), "Modal detail kegiatan seharusnya muncul."
    
    modal_judul = driver.find_element(By.ID, "modalJudul").text
    modal_deskripsi = driver.find_element(By.ID, "modalDeskripsi").text

    assert modal_judul == expected_judul, f"Judul di modal salah. Harusnya '{expected_judul}', tapi yang muncul '{modal_judul}'."
    assert modal_deskripsi == expected_deskripsi, "Deskripsi di modal salah."

    driver.find_element(By.ID, "closeModalBtn").click()
    
    wait.until(EC.invisibility_of_element_located((By.ID, "modalContent")))
    assert not driver.find_element(By.ID, "modalContent").is_displayed(), "Modal seharusnya sudah tertutup."