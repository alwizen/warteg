// src/types/index.ts
export interface Menu {
    id: number;
    nama: string;
    harga: number;
    deskripsi?: string;
    tersedia: boolean;
  }
  
  export interface OrderItem {
    menu_id: number;
    jumlah: number;
    nama: string;
    harga: number;
  }
  
  export interface Customer {
    nama_pelanggan: string;
    nomor_telepon: string;
    alamat: string;
    metode_pembayaran: 'transfer' | 'tempo';
    catatan: string;
  }