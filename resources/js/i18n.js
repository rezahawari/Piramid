import { ref, watch } from 'vue';

// 4 Bahasa yang didukung
export const SUPPORTED_LOCALES = [
    { code: 'id', name: 'Bahasa Indonesia', flag: '🇮🇩', dir: 'ltr' },
    { code: 'en', name: 'English', flag: '🇬🇧', dir: 'ltr' },
    { code: 'zh', name: '中文 (Chinese)', flag: '🇨🇳', dir: 'ltr' },
    { code: 'ar', name: 'العربية (Arabic)', flag: '🇸🇦', dir: 'rtl' },
];

const savedLocale = typeof window !== 'undefined' ? (localStorage.getItem('piramid_lang') || 'id') : 'id';
export const currentLocale = ref(savedLocale);

export const setLocale = (code) => {
    currentLocale.value = code;
    if (typeof window !== 'undefined') {
        localStorage.setItem('piramid_lang', code);
        document.documentElement.lang = code;
        document.documentElement.dir = code === 'ar' ? 'rtl' : 'ltr';
    }
};

// Kamus terjemahan otomatis berbasis Indonesia sebagai bahasa dasar
export const translations = {
    // -------------------------------------------------------------
    // Navigasi & Umum
    // -------------------------------------------------------------
    'Beranda': {
        en: 'Home',
        zh: '首页',
        ar: 'الرئيسية',
    },
    'Pemesanan': {
        en: 'Order',
        zh: '订购',
        ar: 'الطلب',
    },
    'Layanan': {
        en: 'Services',
        zh: '服务',
        ar: 'الخدمات',
    },
    'Transaksi': {
        en: 'Transactions',
        zh: '交易记录',
        ar: 'المعاملات',
    },
    'Admin': {
        en: 'Admin',
        zh: '管理员',
        ar: 'المشرف',
    },
    'Masuk': {
        en: 'Sign In',
        zh: '登录',
        ar: 'تسجيل الدخول',
    },
    'Sign In': {
        en: 'Sign In',
        zh: '登录',
        ar: 'تسجيل الدخول',
    },
    'Daftar': {
        en: 'Sign Up',
        zh: '注册',
        ar: 'تسجيل جديد',
    },
    'Sign Up': {
        en: 'Sign Up',
        zh: '注册',
        ar: 'تسجيل جديد',
    },
    'Keluar': {
        en: 'Log Out',
        zh: '退出',
        ar: 'تسجيل الخروج',
    },
    'Pesan Sekarang': {
        en: 'Order Now',
        zh: '立即订购',
        ar: 'اطلب الآن',
    },
    'Pilih & Pesan Sekarang': {
        en: 'Select & Order Now',
        zh: '选择并立即订购',
        ar: 'اختر واطلب الآن',
    },
    'Mulai dari': {
        en: 'Starting from',
        zh: '起价',
        ar: 'ابتداء من',
    },
    'Hubungi kami': {
        en: 'Contact Us',
        zh: '联系我们',
        ar: 'اتصل بنا',
    },
    'Semua': {
        en: 'All',
        zh: '全部',
        ar: 'الكل',
    },

    // -------------------------------------------------------------
    // Layanan Ibadah
    // -------------------------------------------------------------
    'Qurban': {
        en: 'Qurban',
        zh: '古尔邦节献祭',
        ar: 'قربان',
    },
    'Aqiqah': {
        en: 'Aqiqah',
        zh: '阿齐卡新生儿礼',
        ar: 'عقيقة',
    },
    'Sedekah': {
        en: 'Sadaqah Meat',
        zh: '肉类施舍',
        ar: 'صدقة اللحم',
    },

    // -------------------------------------------------------------
    // Hewan Ternak
    // -------------------------------------------------------------
    'Sapi': {
        en: 'Cow / Cattle',
        zh: '牛',
        ar: 'بقر',
    },
    'Kambing': {
        en: 'Goat',
        zh: '山羊',
        ar: 'ماعز',
    },
    'Domba': {
        en: 'Sheep',
        zh: '绵羊',
        ar: 'خروف / ضأن',
    },
    'Unta': {
        en: 'Camel',
        zh: '骆驼',
        ar: 'إبل',
    },

    // -------------------------------------------------------------
    // Hero & USP
    // -------------------------------------------------------------
    'Pilih Hewan Terbaik Sesuai Syariat': {
        en: 'Choose the Best Animal According to Sharia',
        zh: '根据伊斯兰教法选择最好的牲畜',
        ar: 'اختر أفضل الأضاحي وفقاً للشريعة الإسلامية',
    },
    'Laporan Foto & Video Dokumentasi Realtime': {
        en: 'Real-time Photo & Video Documentation Reports',
        zh: '实时照片与视频记录报告',
        ar: 'تقارير توثيقية بالصور والفيديو في الوقت الفعلي',
    },
    'Penyaluran Tepat Sasaran ke Pelosok & Dhuafa': {
        en: 'Targeted Distribution to Remote Areas & Needy',
        zh: '精准分发至偏远地区与贫困群体',
        ar: 'توزيع موثوق ومباشر للمحتاجين وفي المناطق النائية',
    },
    'Hewan qurban & aqiqah terawat prima, sehat, bersertifikat dinas peternakan dan teruji syar\'i.': {
        en: 'Prime conditioned, healthy livestock certified by veterinary services and compliant with Sharia.',
        zh: '优质健康牲畜，经兽医部门认证，符合伊斯兰教法。',
        ar: 'أضاحي وعقائق ممتازة وصحية ومعتمدة بيطرياً وموافقة للشريعة.',
    },
    'Pantau setiap proses mulai dari penyiapan, penyembelihan atas nama Anda, hingga pembagian daging.': {
        en: 'Monitor every stage from preparation, slaughter in your name, to meat distribution.',
        zh: '全程跟踪从准备、以您的名义宰杀到分发肉类的每个环节。',
        ar: 'تابع كل مرحلة من التجهيز والذبح باسمك حتى توزيع اللحوم.',
    },
    'Menghadirkan senyum kebahagiaan bagi ribuan santri, yatim dhuafa, dan warga prasejahtera.': {
        en: 'Bringing joy and happiness to thousands of students, orphans, and underprivileged families.',
        zh: '为数千名学生、孤儿及贫困家庭带去幸福的笑容。',
        ar: 'رسم البسمة على وجوه آلاف الطلاب والأيتام والأسر المتعففة.',
    },

    // -------------------------------------------------------------
    // Status Transaksi
    // -------------------------------------------------------------
    'Menunggu': {
        en: 'Pending',
        zh: '等待中',
        ar: 'قيد الانتظار',
    },
    'Menunggu Pembayaran': {
        en: 'Waiting for Payment',
        zh: '等待付款',
        ar: 'في انتظار الدفع',
    },
    'Dibayar': {
        en: 'Paid',
        zh: '已付款',
        ar: 'تم الدفع',
    },
    'Lunas': {
        en: 'Paid in Full',
        zh: '已结清',
        ar: 'مدفوع بالكامل',
    },
    'Hewan Disiapkan': {
        en: 'Animal Prepared',
        zh: '牲畜准备中',
        ar: 'تجهيز الأضحية',
    },
    'Tersembelih': {
        en: 'Slaughtered',
        zh: '已屠宰',
        ar: 'تم الذبح شرعاً',
    },
    'Didistribusikan': {
        en: 'Distributed',
        zh: '已分发',
        ar: 'تم التوزيع',
    },

    // -------------------------------------------------------------
    // Distribusi & Opsi
    // -------------------------------------------------------------
    'Disalurkan oleh Piramid': {
        en: 'Distributed by Piramid Foundation',
        zh: '由金字塔基金会分发',
        ar: 'توزيع بواسطة مؤسسة بيراميد',
    },
    'Kirim ke Alamat Sendiri': {
        en: 'Deliver to My Address',
        zh: '配送至我的地址',
        ar: 'توصيل إلى عنواني الخاص',
    },
};

/**
 * Fungsi helper translate otomatis:
 * @param {string} text - Teks bahasa Indonesia dasar
 * @returns {string} - Teks dalam bahasa yang sedang dipilih
 */
export const t = (text) => {
    if (!text) return '';
    const loc = currentLocale.value;
    if (loc === 'id') return text; // Bahasa dasar

    const match = translations[text.trim()];
    if (match && match[loc]) {
        return match[loc];
    }

    // Jika belum ada di kamus, fallback ke bahasa Indonesia
    return text;
};

// Inisialisasi awal atribut dokumen
if (typeof window !== 'undefined') {
    document.documentElement.lang = currentLocale.value;
    document.documentElement.dir = currentLocale.value === 'ar' ? 'rtl' : 'ltr';
}
