@extends('layouts.app')

@section('title', 'About')

@section('content')
    <div class="container">
        <!-- Judul Halaman -->
        <header>
            <h1 style="font-size: 50px; font-weight: bold; color: white; text-align: center;">Alat Pendeteksi Ketinggian Air Sumur</h1>
        </header>
        
        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('monairsu/public/img/Alat.png') }}" alt="Alat" style="width: 600px;">
        </div>
        
        
        <!-- Alamat -->
        <p style="font-size: 20px; font-weight: bold; color: white; text-align: center; margin-bottom: 20px;">
            Alamat: Desa Sindangkerta, Kecamatan Sindangkerta, Kabupaten Bandung Barat, Jawa Barat, Indonesia
        </p>
        
       
        
        <!-- Informasi Organisasi -->
        <section class="info-section" style="margin-top: 20px;">
            <p style="font-size: 20px; font-weight: bold; color: white; text-align: justify;">
            Sistem monitoring ketinggian air sumur menggunakan beberapa komponen utama untuk memastikan pengukuran yang akurat dan efisien. Sensor Infrared SEN0366 adalah perangkat yang mengukur jarak dengan menggunakan sinar laser, memungkinkan pemantauan kedalaman air sumur dengan presisi tinggi, bahkan dalam kondisi pencahayaan yang bervariasi. Mikrokontroler ESP32 bertindak sebagai pusat pengolahan data, mengumpulkan informasi dari sensor dan mengirimkannya ke server melalui koneksi Wi-Fi atau Bluetooth, berkat kemampuannya yang kuat dan konektivitas yang handal. Layar LCD I2C menampilkan data level air secara real-time, terhubung dengan mikrokontroler melalui antarmuka I2C, sehingga memudahkan pemantauan visual tanpa perangkat tambahan. Untuk menyuplai daya, adapter 5V 2A mengkonversi listrik dari sumber utama menjadi 5V DC yang stabil, memastikan semua komponen sistem beroperasi dengan lancar dan efisien.
            </p>
        </section>
    </div>
@endsection
