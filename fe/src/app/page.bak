'use client'

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import axios from 'axios';
import { Menu, OrderItem, Customer } from '@/types';

export default function Home() {
  const router = useRouter();
  const [menus, setMenus] = useState<Menu[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [orderItems, setOrderItems] = useState<OrderItem[]>([]);
  const [customer, setCustomer] = useState<Customer>({
    nama_pelanggan: '',
    nomor_telepon: '',
    alamat: '',
    metode_pembayaran: 'transfer',
    catatan: '',
  });

  useEffect(() => {
    const fetchMenus = async () => {
      try {
        const response = await axios.get(`${process.env.NEXT_PUBLIC_API_URL}/api/menus`);
        setMenus(response.data);
        setLoading(false);
      } catch (error) {
        console.error('Error fetching menus:', error);
        setLoading(false);
      }
    };

    fetchMenus();
  }, []);

  const handleAddItem = (menuId: number) => {
    const menu = menus.find(m => m.id === menuId);
    if (!menu) return;

    const existingItem = orderItems.find(item => item.menu_id === menuId);

    if (existingItem) {
      setOrderItems(orderItems.map(item => 
        item.menu_id === menuId 
          ? { ...item, jumlah: item.jumlah + 1 } 
          : item
      ));
    } else {
      setOrderItems([...orderItems, {
        menu_id: menuId,
        jumlah: 1,
        nama: menu.nama,
        harga: menu.harga
      }]);
    }
  };

  const handleRemoveItem = (menuId: number) => {
    const existingItem = orderItems.find(item => item.menu_id === menuId);

    if (existingItem && existingItem.jumlah > 1) {
      setOrderItems(orderItems.map(item => 
        item.menu_id === menuId 
          ? { ...item, jumlah: item.jumlah - 1 } 
          : item
      ));
    } else {
      setOrderItems(orderItems.filter(item => item.menu_id !== menuId));
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setCustomer({ ...customer, [name]: value });
  };

  const calculateTotal = () => {
    return orderItems.reduce((sum, item) => sum + (item.jumlah * item.harga), 0);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (orderItems.length === 0) {
      alert('Silakan pilih minimal 1 menu!');
      return;
    }

    if (!customer.nama_pelanggan || !customer.nomor_telepon) {
      alert('Nama dan nomor telepon harus diisi!');
      return;
    }

    const orderData = {
      ...customer,
      items: orderItems.map(item => ({
        menu_id: item.menu_id,
        jumlah: item.jumlah
      }))
    };

    try {
      setLoading(true);
      const response = await axios.post(`${process.env.NEXT_PUBLIC_API_URL}/api/orders`, orderData);
      const orderInfo = response.data.order;
      
      // Format telepon untuk WhatsApp
      let phoneNumber = '08981966660';
      if (phoneNumber.startsWith('0')) {
        phoneNumber = '62' + phoneNumber.substring(1);
      }
      
      // Buat pesan WhatsApp
      const total = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(orderInfo.total);
      const message = encodeURIComponent(`Saya sudah melakukan order dengan no order: ${orderInfo.id}, total: ${total} dan pembayaran: ${orderInfo.metode_pembayaran === 'transfer' ? 'Transfer' : 'Tempo'}`);
      
      // Redirect ke WhatsApp
      window.location.href = `https://wa.me/${phoneNumber}?text=${message}`;
    } catch (error) {
      console.error('Error submitting order:', error);
      setLoading(false);
      alert('Terjadi kesalahan saat membuat order. Silakan coba lagi.');
    }
  };

  return (
    <div className="p-4 max-w-6xl mx-auto my-8">
      <h1 className="text-5xl font-bold text-center mb-8 bg-yellow-300 p-4 border-4 border-black transform -rotate-1 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] uppercase">
        WARTEG ONLINE
      </h1>
      
      {loading ? (
        <div className="text-center text-2xl font-bold p-8 bg-yellow-300 border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
          Loading...
        </div>
      ) : (
        <div className="bg-white border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] p-8">
          <form onSubmit={handleSubmit}>
            <div className="mb-8 p-6 bg-gray-50 border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
              <h2 className="text-2xl font-bold mb-4 uppercase">DATA PELANGGAN</h2>
              <div className="mb-4">
                <label htmlFor="nama_pelanggan" className="block mb-2 font-bold">Nama Lengkap</label>
                <input
                  type="text"
                  id="nama_pelanggan"
                  name="nama_pelanggan"
                  className="w-full p-2 border-4 border-black focus:ring-4 focus:ring-yellow-300"
                  value={customer.nama_pelanggan}
                  onChange={handleInputChange}
                  required
                />
              </div>

              <div className="mb-4">
                <label htmlFor="nomor_telepon" className="block mb-2 font-bold">Nomor Telepon</label>
                <input
                  type="tel"
                  id="nomor_telepon"
                  name="nomor_telepon"
                  className="w-full p-2 border-4 border-black focus:ring-4 focus:ring-yellow-300"
                  value={customer.nomor_telepon}
                  onChange={handleInputChange}
                  required
                />
              </div>

              <div className="mb-4">
                <label htmlFor="alamat" className="block mb-2 font-bold">Alamat</label>
                <textarea
                  id="alamat"
                  name="alamat"
                  className="w-full p-2 border-4 border-black focus:ring-4 focus:ring-yellow-300"
                  value={customer.alamat}
                  onChange={handleInputChange}
                />
              </div>

              <div className="mb-4">
                <label htmlFor="metode_pembayaran" className="block mb-2 font-bold">Metode Pembayaran</label>
                <select
                  id="metode_pembayaran"
                  name="metode_pembayaran"
                  className="w-full p-2 border-4 border-black focus:ring-4 focus:ring-yellow-300"
                  value={customer.metode_pembayaran}
                  onChange={handleInputChange}
                >
                  <option value="transfer">Transfer</option>
                  <option value="tempo">Bayar Nanti (Tempo - Max 1 Minggu)</option>
                </select>
              </div>

              <div className="mb-4">
                <label htmlFor="catatan" className="block mb-2 font-bold">Catatan</label>
                <textarea
                  id="catatan"
                  name="catatan"
                  className="w-full p-2 border-4 border-black focus:ring-4 focus:ring-yellow-300"
                  value={customer.catatan}
                  onChange={handleInputChange}
                />
              </div>
            </div>

            <div className="mb-8 p-6 bg-gray-50 border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
              <h2 className="text-2xl font-bold mb-4 uppercase">MENU WARTEG</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {menus.map((menu) => (
                  <div key={menu.id} className="border-4 border-black p-4 bg-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <div className="mb-2">
                      <h3 className="text-xl font-bold">{menu.nama}</h3>
                      <p className="text-lg font-bold text-green-600">
                        {new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(menu.harga)}
                      </p>
                      {menu.deskripsi && <p className="text-gray-700">{menu.deskripsi}</p>}
                    </div>
                    
                    <div className="flex items-center mt-4">
                      <button 
                        type="button" 
                        className="bg-green-500 text-white font-bold py-1 px-3 border-4 border-black hover:bg-green-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                        onClick={() => handleAddItem(menu.id)}
                      >
                        +
                      </button>
                      
                      {orderItems.find(item => item.menu_id === menu.id) && (
                        <>
                          <span className="mx-3 font-bold">
                            {orderItems.find(item => item.menu_id === menu.id)?.jumlah}
                          </span>
                          <button 
                            type="button" 
                            className="bg-red-500 text-white font-bold py-1 px-3 border-4 border-black hover:bg-red-600 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                            onClick={() => handleRemoveItem(menu.id)}
                          >
                            -
                          </button>
                        </>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {orderItems.length > 0 && (
              <div className="mb-8 p-6 bg-gray-50 border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <h2 className="text-2xl font-bold mb-4 uppercase">PESANAN ANDA</h2>
                <div className="space-y-2">
                  {orderItems.map((item) => (
                    <div key={item.menu_id} className="flex justify-between border-b-2 border-gray-200 py-2">
                      <span>{item.nama} x {item.jumlah}</span>
                      <span className="font-bold">
                        {new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.jumlah * item.harga)}
                      </span>
                    </div>
                  ))}
                  <div className="flex justify-between pt-4 font-bold text-xl">
                    <span>TOTAL</span>
                    <span>
                      {new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(calculateTotal())}
                    </span>
                  </div>
                </div>
              </div>
            )}

            <button 
              type="submit" 
              className="w-full py-3 bg-yellow-300 border-4 border-black text-xl font-bold hover:bg-yellow-400 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] disabled:opacity-50"
              disabled={loading || orderItems.length === 0}
            >
              {loading ? 'Processing...' : 'PESAN SEKARANG'}
            </button>
          </form>
        </div>
      )}
    </div>
  );
}