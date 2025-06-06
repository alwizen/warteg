import type { Metadata } from 'next'
import './globals.css'

export const metadata: Metadata = {
  title: 'Warteg Online Order',
  description: 'Aplikasi pemesanan warteg online',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="id">
      <body>{children}</body>
    </html>
  )
}