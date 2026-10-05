# 📱 Dokumentasi API & Panduan Integrasi Mobile App (Piramid)

Dokumen ini berisi spesifikasi teknis lengkap untuk tim mobile developer dalam mengimplementasikan penyesuaian:
1. **Multi-Mata Uang & Bahasa** (`IDR`, `USD`, `CNY`, `SAR`).
2. **Pilihan Distribusi / Penyaluran Dinamis** (diatur dari backend).
3. **Varian Hewan Fleksibel** (Bobot, Ras/Jenis, Kelas Super/Reguler, dll).
4. **Opsi Pengolahan Daging (Mentah / Matang)** dengan Biaya Dinamis.

---

## 🌐 1. Base URL & Header Standard

* **Base URL Production:** `https://api.piramidqurban.com/api/v1` (atau domain Dokploy Anda)
* **Headers:**
  ```http
  Accept: application/json
  Content-Type: application/json
  Authorization: Bearer <SANCTUM_TOKEN>
  ```
* **Query Parameter Bahasa (`lang`):**
  * `?lang=id` (Indonesia - default, mata uang IDR / Rp)
  * `?lang=en` (English, mata uang USD / $)
  * `?lang=zh` (Chinese, mata uang CNY / ¥)
  * `?lang=ar` (Arabic, mata uang SAR / ﷼)

---

## 🛍️ 2. Endpoint Katalog & Layanan

### A. Ambil Semua Layanan & Produk Hewan
* **Method:** `GET`
* **Endpoint:** `/services?lang=id`

**Contoh Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Qurban Berkah",
      "slug": "qurban",
      "description": "Layanan ibadah qurban terpercaya...",
      "cover_image_url": "https://...",
      "has_sohibul": true,
      "has_cooking_option": true,
      "default_cooking_option": "raw",
      "cooking_fee": {
        "idr": 1000000,
        "usd": 65,
        "cny": 460,
        "sar": 245
      },
      "products": [
        {
          "id": 10,
          "name": "Domba Premium",
          "slug": "domba-premium",
          "description": "Domba jantan sehat bebas cacat...",
          "prices": {
            "idr": 2800000,
            "usd": 185,
            "cny": 1300,
            "sar": 690
          },
          "weight_estimate_kg": 35.0,
          "stock": 25,
          "max_sohibul": 1,
          "primary_image_url": "https://...",
          "variants": [
            {
              "id": 101,
              "name": "Tipe A (Super)",
              "spec_description": "35-40 kg, Jantan",
              "prices": {
                "idr": 3200000,
                "usd": 210,
                "cny": 1490,
                "sar": 790
              },
              "stock": 10
            },
            {
              "id": 102,
              "name": "Tipe B (Standar)",
              "spec_description": "28-32 kg, Jantan",
              "prices": {
                "idr": 2800000,
                "usd": 185,
                "cny": 1300,
                "sar": 690
              },
              "stock": 15
            }
          ]
        }
      ]
    }
  ]
}
```

---

### B. Detail Produk & Opsi Distribusi
* **Method:** `GET`
* **Endpoint:** `/services/{service_slug}/products/{product_slug}?lang=id`

**Contoh Response:**
```json
{
  "service": {
    "id": 1,
    "name": "Qurban",
    "slug": "qurban",
    "has_sohibul": true,
    "has_cooking_option": true,
    "default_cooking_option": "raw",
    "cooking_fee": {
      "idr": 1000000,
      "usd": 65,
      "cny": 460,
      "sar": 245
    }
  },
  "product": {
    "id": 10,
    "name": "Domba Premium",
    "slug": "domba-premium",
    "description": "...",
    "prices": {
      "idr": 2800000,
      "usd": 185,
      "cny": 1300,
      "sar": 690
    },
    "stock": 25,
    "max_sohibul": 1,
    "primary_image_url": "https://...",
    "gallery": ["https://...", "https://..."],
    "variants": [
      {
        "id": 101,
        "name": "Tipe A (Super)",
        "spec_description": "35-40 kg, Jantan",
        "prices": {
          "idr": 3200000,
          "usd": 210,
          "cny": 1490,
          "sar": 790
        },
        "stock": 10
      }
    ]
  },
  "distribution_options": [
    {
      "id": 1,
      "name": "Indonesia (Pelosok & Dhuafa)",
      "description": "Penyaluran langsung ke warga prasejahtera pelosok nusantara.",
      "fees": {
        "idr": 0,
        "usd": 0,
        "cny": 0,
        "sar": 0
      }
    },
    {
      "id": 2,
      "name": "Makkah / Tanah Suci",
      "description": "Penyembelihan dan pembagian daging di wilayah Tanah Suci Makkah.",
      "fees": {
        "idr": 350000,
        "usd": 25,
        "cny": 175,
        "sar": 90
      }
    }
  ]
}
```

---

## 🛒 3. Endpoint Checkout & Pemesanan

### A. Ambil Opsi Distribusi & Payment
* **Method:** `GET`
* **Endpoint:** `/checkout/options?lang=id`
* **Auth:** Required (`Bearer Token`)

**Response:**
```json
{
  "distribution_options": [
    {
      "id": 1,
      "name": "Indonesia (Pelosok & Dhuafa)",
      "description": "Distribusi ke pesantren dan warga dhuafa.",
      "fee": {
        "idr": 0,
        "usd": 0,
        "cny": 0,
        "sar": 0
      }
    },
    {
      "id": 2,
      "name": "Makkah / Tanah Suci",
      "description": "Distribusi di Tanah Suci.",
      "fee": {
        "idr": 350000,
        "usd": 25,
        "cny": 175,
        "sar": 90
      }
    }
  ],
  "payment_options": [
    { "value": "midtrans", "label": "Online Payment Gateway (Midtrans)" },
    { "value": "manual_transfer", "label": "Transfer Bank Manual" }
  ]
}
```

---

### B. Submit Pesanan (Checkout Store)
* **Method:** `POST`
* **Endpoint:** `/checkout`
* **Auth:** Required (`Bearer Token`)

**Payload Request (JSON):**
```json
{
  "service_id": 1,
  "product_id": 10,
  "product_variant_id": 101,
  "distribution_option_id": 1,
  "quantity": 1,
  "cooking_option": "cooked",
  "currency": "IDR",
  "distribution_location_note": "Doa berkah untuk keluarga kami",
  "sohibul_names": [
    "Ahmad Reza bin Abdullah"
  ],
  "payment_method": "midtrans"
}
```

#### 💡 Aturan Bisnis Perhitungan Harga di Mobile Client:
1. **Harga Unit:**
   * Jika user memilih varian $\rightarrow$ ambil harga varian sesuai `currency` yang aktif.
   * Jika tidak ada varian $\rightarrow$ ambil harga produk sesuai `currency` yang aktif.
2. **Biaya Olahan (`cooking_fee`):**
   * Jika `cooking_option == service.default_cooking_option` $\rightarrow$ Biaya = **0** (Termasuk).
   * Jika `cooking_option != service.default_cooking_option` $\rightarrow$ Biaya = `service.cooking_fee[currency]`.
3. **Biaya Distribusi (`distribution_fee`):**
   * Ambil `distribution.fee[currency]`.
4. **Total Pembayaran:**
   $$\text{Total} = (\text{Unit Price} + \text{Cooking Fee} + \text{Distribution Fee}) \times \text{Quantity}$$

**Response Sukses (`201 Created`):**
```json
{
  "message": "Pesanan berhasil dibuat. Silakan selesaikan pembayaran.",
  "transaction": {
    "id": 42,
    "transaction_code": "QUR-20261004-9821",
    "currency": "IDR",
    "unit_price": 3200000,
    "cooking_fee": 1000000,
    "distribution_fee": 0,
    "total_amount": 4200000,
    "status": "menunggu",
    "status_label": "Menunggu",
    "payment_status": "pending",
    "payment_status_label": "Menunggu Pembayaran",
    "payment_method": "midtrans",
    "payment_method_label": "Online Payment Gateway (Midtrans)",
    "created_at": "2026-10-04T10:50:00.000000Z"
  }
}
```

---

## 💳 4. Endpoint Pembayaran

### A. Ambil Midtrans Snap Token
* **Method:** `POST`
* **Endpoint:** `/transactions/{transaction_code}/snap-token`
* **Response:**
  ```json
  {
    "snap_token": "xxxx-xxxx-xxxx-xxxx",
    "redirect_url": "https://app.sandbox.midtrans.com/snap/v2/vtweb/xxxx"
  }
  ```

### B. Upload Bukti Transfer Manual
* **Method:** `POST` (Multipart / Form-Data)
* **Endpoint:** `/transactions/{transaction_code}/manual-transfer-proof`
* **Body:** `proof_file` (File Image/PDF maks 5MB)
