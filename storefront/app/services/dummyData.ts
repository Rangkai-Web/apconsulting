import type { Category, Product } from "~/types";

// Dummy data only. Replace with real API calls once api/ endpoints exist.

export const dummyCategories: Category[] = [
  {
    id: 1,
    name: "Hidrasi & Pelembab",
    slug: "hidrasi-pelembab",
    parentId: null,
  },
  { id: 2, name: "Sheet Mask", slug: "sheet-mask", parentId: null },
  { id: 3, name: "Perawatan Alpine", slug: "perawatan-alpine", parentId: null },
  { id: 4, name: "Layanan Facial", slug: "layanan-facial", parentId: null },
];

export const dummyProducts: Product[] = [
  {
    id: 1,
    categoryId: 1,
    name: "Alpine Moisture Cream",
    slug: "alpine-moisture-cream",
    description:
      "Krim pelembab mewah dengan kandungan ekstrak tumbuhan alpine untuk hidrasi mendalam, mengunci kelembaban kulit sepanjang hari, dan memperkuat skin barrier.",
    images: [
      "https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&w=640&q=80",
      "https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?auto=format&fit=crop&w=640&q=80",
      "https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=640&q=80",
    ],
    isActive: true,
    ratingAvg: 4.8,
    ratingCount: 128,
    variants: [
      {
        id: 101,
        productId: 1,
        name: "50ml",
        sku: "DRP-AMC-50",
        price: 249000,
        stock: 80,
        weight: 150,
        image:
          "https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&w=640&q=80",
      },
      {
        id: 102,
        productId: 1,
        name: "100ml",
        sku: "DRP-AMC-100",
        price: 429000,
        stock: 45,
        weight: 250,
        image:
          "https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?auto=format&fit=crop&w=640&q=80",
      },
      {
        id: 103,
        productId: 1,
        name: "150ml",
        sku: "DRP-AMC-150",
        price: 589000,
        stock: 0,
        weight: 350,
        image:
          "https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=640&q=80",
      },
    ],
  },
  {
    id: 2,
    categoryId: 1,
    name: "Dendrobium Hydrating Serum",
    slug: "dendrobium-hydrating-serum",
    description:
      "Serum konsentrat premium dengan ekstrak anggrek Dendrobium alpine untuk hidrasi instan, mengenyalkan kulit kusam, dan menyamarkan tanda-tanda penuaan dini.",
    images: [
      "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=640&q=80",
      "https://images.unsplash.com/photo-1601049541289-9b1b7bbbfe19?auto=format&fit=crop&w=640&q=80",
    ],
    isActive: true,
    ratingAvg: 4.9,
    ratingCount: 64,
    variants: [
      {
        id: 104,
        productId: 2,
        name: "30ml",
        sku: "DRP-DHS-30",
        price: 299000,
        stock: 60,
        weight: 120,
        image:
          "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=640&q=80",
      },
      {
        id: 105,
        productId: 2,
        name: "50ml",
        sku: "DRP-DHS-50",
        price: 459000,
        stock: 30,
        weight: 180,
        image:
          "https://images.unsplash.com/photo-1601049541289-9b1b7bbbfe19?auto=format&fit=crop&w=640&q=80",
      },
    ],
  },
  {
    id: 3,
    categoryId: 2,
    name: "Alpine Leaf Hydrating Mask",
    slug: "alpine-leaf-hydrating-mask",
    description:
      "Sheet mask ultra-thin yang kaya akan serum nutrisi tanaman alpine, memberikan hidrasi intensif secara mendalam dan mencerahkan kulit lelah dalam waktu 15 menit.",
    images: [
      "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=640&q=80",
      "https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&w=640&q=80",
    ],
    isActive: true,
    ratingAvg: 4.7,
    ratingCount: 88,
    variants: [
      {
        id: 106,
        productId: 3,
        name: "1 Pcs",
        sku: "DRP-ALM-01",
        price: 29000,
        stock: 120,
        weight: 30,
        image:
          "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=640&q=80",
      },
      {
        id: 107,
        productId: 3,
        name: "Box (5 Pcs)",
        sku: "DRP-ALM-05",
        price: 125000,
        stock: 45,
        weight: 150,
        image:
          "https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&w=640&q=80",
      },
    ],
  },
  {
    id: 4,
    categoryId: 2,
    name: "Centella Soothing Sheet Mask",
    slug: "centella-soothing-sheet-mask",
    description:
      "Masker lembaran yang diformulasikan dengan Centella Asiatica dari pegunungan alpine untuk menenangkan kulit kemerahan, mengurangi iritasi, serta meredakan kulit sensitif.",
    images: [
      "https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=640&q=80",
    ],
    isActive: true,
    ratingAvg: 4.6,
    ratingCount: 19,
    variants: [
      {
        id: 108,
        productId: 4,
        name: "Box (5 Pcs)",
        sku: "DRP-CSS-05",
        price: 135000,
        stock: 15,
        weight: 150,
        image:
          "https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=640&q=80",
      },
    ],
  },
  {
    id: 5,
    categoryId: 3,
    name: "Snow Lotus Brightening Essence",
    slug: "snow-lotus-brightening-essence",
    description:
      "Essence cair dengan ekstrak bunga Snow Lotus alpine langka untuk memudarkan noda hitam, meratakan warna kulit yang kusam, dan mengembalikan pancaran kilau sehat alami kulit.",
    images: [
      "https://images.unsplash.com/photo-1601049541289-9b1b7bbbfe19?auto=format&fit=crop&w=640&q=80",
    ],
    isActive: true,
    ratingAvg: 4.8,
    ratingCount: 42,
    variants: [
      {
        id: 109,
        productId: 5,
        name: "150ml",
        sku: "DRP-SLB-150",
        price: 389000,
        stock: 12,
        weight: 350,
        image:
          "https://images.unsplash.com/photo-1601049541289-9b1b7bbbfe19?auto=format&fit=crop&w=640&q=80",
      },
    ],
  },
  {
    id: 6,
    categoryId: 3,
    name: "Ginseng Anti-Aging Eye Cream",
    slug: "ginseng-anti-aging-eye-cream",
    description:
      "Krim mata bernutrisi tinggi dengan ekstrak Ginseng alpine untuk mengencangkan area sekitar mata, mengurangi lingkaran hitam, kantung mata, serta garis halus.",
    images: [
      "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=640&q=80",
    ],
    isActive: true,
    ratingAvg: 4.7,
    ratingCount: 53,
    variants: [
      {
        id: 110,
        productId: 6,
        name: "20g",
        sku: "DRP-GEC-20",
        price: 349000,
        stock: 20,
        weight: 80,
        image:
          "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=640&q=80",
      },
    ],
  },
  {
    id: 7,
    categoryId: 4,
    name: "Alpine Deep Hydration Facial Treatment",
    slug: "alpine-deep-hydration-facial-treatment",
    description:
      "Layanan facial treatment profesional menggunakan serum ekstrak tumbuhan alpine eksklusif dan alat ultrasound modern untuk menutrisi kulit secara mendalam dan instan dewy glow.",
    images: [
      "https://images.unsplash.com/photo-1519699047748-de8e457a634e?auto=format&fit=crop&w=640&q=80",
      "https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=640&q=80",
    ],
    isActive: true,
    ratingAvg: 4.9,
    ratingCount: 210,
    variants: [
      {
        id: 111,
        productId: 7,
        name: "Single Session (60 Min)",
        sku: "DRP-FCL-DH1",
        price: 350000,
        stock: 100,
        weight: 0,
        image:
          "https://images.unsplash.com/photo-1519699047748-de8e457a634e?auto=format&fit=crop&w=640&q=80",
      },
      {
        id: 112,
        productId: 7,
        name: "Package 3x Sessions",
        sku: "DRP-FCL-DH3",
        price: 950000,
        stock: 40,
        weight: 0,
        image:
          "https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=640&q=80",
      },
    ],
  },
  {
    id: 8,
    categoryId: 4,
    name: "Alpine Brightening & Detox Facial",
    slug: "alpine-brightening-detox-facial",
    description:
      "Perawatan facial detoksifikasi wajah dengan masker bunga Snow Lotus alami dan ekstraksi komedo menyeluruh untuk kulit cerah bebas kusam.",
    images: [
      "https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=640&q=80",
    ],
    isActive: true,
    ratingAvg: 4.8,
    ratingCount: 36,
    variants: [
      {
        id: 113,
        productId: 8,
        name: "Single Session (75 Min)",
        sku: "DRP-FCL-BD1",
        price: 420000,
        stock: 70,
        weight: 0,
        image:
          "https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=640&q=80",
      },
    ],
  },
  {
    id: 9,
    categoryId: 1,
    name: "Alpine Plant Hydration Toner",
    slug: "alpine-plant-hydration-toner",
    description:
      "Toner pelembab berbahan dasar ekstrak air tanaman alpine murni untuk mengembalikan kelembaban alami kulit setelah mencuci muka dan menyeimbangkan pH kulit.",
    images: [
      "https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&w=640&q=80",
    ],
    isActive: true,
    ratingAvg: 4.6,
    ratingCount: 28,
    variants: [
      {
        id: 114,
        productId: 9,
        name: "200ml",
        sku: "DRP-AHT-200",
        price: 189000,
        stock: 50,
        weight: 280,
        image:
          "https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&w=640&q=80",
      },
    ],
  },
];
