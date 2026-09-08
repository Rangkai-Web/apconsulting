import { ref, computed, type Ref } from "vue";

export type Locale = "en" | "id";

export interface LanguageOption {
  code: Locale;
  label: string;
  shortLabel: string;
  flag: string;
}

export const LANGUAGES: LanguageOption[] = [
  {
    code: "en",
    label: "English",
    shortLabel: "EN",
    flag: "/images/icon/UnitedFlagIcon.svg",
  },
  {
    code: "id",
    label: "Bahasa Indonesia",
    shortLabel: "ID",
    flag: "/images/icon/IndonesiaFlagIcon.svg",
  },
];

const translations: Record<Locale, Record<string, string>> = {
  en: {
    "nav.home": "Home",
    "nav.services": "Services",
    "nav.insights": "Insights",
    "nav.about": "About",
    "nav.contact": "Contact",
    "nav.discussNeeds": "Discuss Your Needs",
    "cta.tag": "START A CONVERSATION",
    "cta.titleNormal": "Ready to bring more clarity to your business?",
    "cta.titleItalic": "Let's take the next step together.",
    "cta.description":
      "Tell us about your tax, accounting, or financial needs. PCG will help you identify a practical starting point for your business.",
    "cta.buttonText": "Discuss Your Needs",
    "footer.tagline":
      "Professional support in tax, accounting, and financial services. We're helping businesses move forward with clarity and confidence.",
    "footer.rights": "All Rights Reserved",
    "footer.quickLinks": "Quick Links",
    "footer.services": "Services",
    "footer.contactUs": "Contact Us",

    // Home Hero
    "home.hero.title1": "CLEARER FINANCES.",
    "home.hero.titleHighlight": "STRONGER BUSINESS",
    "home.hero.title2": "DECISIONS.",
    "home.hero.description":
      "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals",
    "home.hero.exploreBtn": "EXPLORE OUR SERVICES",
    "home.hero.discussBtn": "DISCUSS YOUR NEEDS",

    // Home About PCG
    "home.about.tag": "ABOUT PCG",
    "home.about.titleNormal": "More than accountants.",
    "home.about.titleItalic": "Partners in your progress.",
    "home.about.description":
      "We helps businesses, families, and ambitious individuals make confident financial decisions. We turn complex information into clear, practical next steps, helping you stay compliant, improve performance, and plan for what comes next.",
    "home.about.button": "DISCOVER HOW WE HELP",

    // Home Stats
    "home.stats.tag": "PCG AT A GLANCE",
    "home.stats.titleItalic": "A structured foundation",
    "home.stats.titleNormal": "for better business decisions.",
    "home.stats.description":
      "Key facts about our service model and approach—presented without unsupported client or performance claims.",
    "home.stats.stat1": "Core service areas",
    "home.stats.stat2": "Steps in our approach",
    "home.stats.stat3": "Integrated support partner",
    "home.stats.stat4": "Customers",

    // Home How We Help
    "home.howWeHelp.tag": "HOW WE HELP",
    "home.howWeHelp.titleItalic": "Your Business Deserves",
    "home.howWeHelp.titleNormal": "More Than Routine Bookkeeping.",
    "home.howWeHelp.description":
      "We help businesses manage tax, accounting, bookkeeping, and financial reporting with clarity and structure, so you can make confident decisions and focus on growth.",
    "home.howWeHelp.card1Title": "Compliance with confidence.",
    "home.howWeHelp.card1Body":
      "Tax compliance, filings, reviews, tax planning, and audit support—handled with care and clarity",
    "home.howWeHelp.card2Title": "Build a Stronger Financial Foundation",
    "home.howWeHelp.card2Body":
      "Accurate bookkeeping, financial reporting, bank reconciliation, and journal adjustments to keep your business organized.",
    "home.howWeHelp.card3Title": "Insight for better decisions.",
    "home.howWeHelp.card3Body":
      "Financial analysis, cash-flow forecasts, budgeting, and performance reviews to help you plan ahead.",

    // Home Why Choose Us
    "home.whyChooseUs.tag": "WHY BUSINESSES CHOOSE US",
    "home.whyChooseUs.titleItalic": "The right support",
    "home.whyChooseUs.titleNormal":
      "can change the way your business moves forward.",
    "home.whyChooseUs.description":
      "Managing a business requires more than keeping records up to date. It requires clear information, practical guidance, and a reliable partner who understands the bigger picture.",
    "home.whyChooseUs.card1Title": "Tax Advisory",
    "home.whyChooseUs.card1Body":
      "Manage obligations, identify risks, and make more informed tax decisions.",
    "home.whyChooseUs.card2Title": "Finance & Consulting",
    "home.whyChooseUs.card2Body":
      "Use financial visibility to plan ahead and build sustainable growth.",

    // Home Our Approach
    "home.ourApproach.tag": "OUR APPROACH",
    "home.ourApproach.titlePrefix": "From ",
    "home.ourApproach.titleHighlight": "Financial Clarity",
    "home.ourApproach.titleSuffix": "to Confident Action.",
    "home.ourApproach.subtitle":
      "A structured approach to help your business stay compliant, organized, and ready for what comes next.",
    "home.ourApproach.step1Title": "Understand Your Business",
    "home.ourApproach.step1Body":
      "We assess your financial position, records, and administrative processes to understand where your business stands today.",
    "home.ourApproach.step2Title": "Identify What Matters",
    "home.ourApproach.step2Body":
      "We identify risks, gaps, and priorities, then focus on the areas that need attention first.",
    "home.ourApproach.step3Title": "Put Solutions in Place",
    "home.ourApproach.step3Body":
      "We implement the agreed tax, accounting, and financial solutions based on your business needs.",
    "home.ourApproach.step4Title": "Review and Move Forward",
    "home.ourApproach.step4Body":
      "We review progress regularly and provide ongoing support as your business evolves.",

    // Home Insights
    "home.insights.tag": "INSIGHTS",
    "home.insights.titleNormal": "Practical insight for better",
    "home.insights.titleItalic": "business decisions.",
    "home.insights.subtitle":
      "Clear perspectives on tax, accounting, and financial management, made for businesses that want to move forward with confidence.",
    "home.insights.excerpt":
      "Clear perspectives on tax, accounting, and financial management, made for businesses that want to move forward with confidence.",
    "home.insights.viewDetails": "View Details",
    "home.insights.post1Title":
      "What your financial reports are really telling you",
    "home.insights.post2Title": "Why monthly financial reporting matters",
    "home.insights.post3Title":
      "A practical way to improve cash-flow visibility",
    "home.insights.cat1": "Financial Management",
    "home.insights.cat2": "Accounting",
    "home.insights.cat3": "Finance",

    // Home FAQ
    "home.faq.tag": "FREQUENTLY ASKED QUESTIONS",
    "home.faq.titlePrefix": "Clear answers ",
    "home.faq.titleHighlight": "for your next step.",
    "home.faq.subtitle":
      "Before starting a conversation, here are answers to the questions businesses commonly ask about PCG's services, process, and engagement.",
    "home.faq.q1": "What services does PCG provide?",
    "home.faq.a1":
      "PCG provides professional consulting and advisory support across taxation, accounting and bookkeeping, and other customized financial services. The available scope depends on your business needs and will be discussed before an engagement begins.",
    "home.faq.q2": "How does PCG work with us?",
    "home.faq.a2":
      "We start with an initial consultation to assess your business structure, records, and primary challenges. From there, we propose a customized scope of work tailored to your objectives and operational scale.",
    "home.faq.q3": "What information should I prepare before contacting PCG?",
    "home.faq.a3":
      "Having basic information ready—such as your company structure, current accounting setup, recent financial reports, or tax documents—helps us understand your situation faster, though you are welcome to contact us even at an early stage.",
    "home.faq.q4":
      "Does PCG support ongoing monthly services or only one-off projects?",
    "home.faq.a4":
      "We support both ongoing recurring retainer engagements (such as monthly bookkeeping or tax reporting) as well as project-based advisory services depending on your company's requirements.",
    "home.faq.q5": "How is engagement pricing set up?",
    "home.faq.a5":
      "Pricing is customized based on the scope, complexity, and transaction volume of your business operations. We provide a transparent scope and pricing proposal after our initial discussion.",

    // About Hero
    "about.hero.title1": "More than accountants.",
    "about.hero.title2": "Partners in your progress.",
    "about.hero.description":
      "PCG helps businesses make confident financial decisions. From tax and accounting to financial planning and business support, we turn complex information into clear, practical next steps.",
    "about.hero.discoverBtn": "DISCOVER OUR APPROACH",
    "about.hero.teamBtn": "MEET THE TEAM",

    // About PCG Section
    "about.pcg.tag": "ABOUT PCG",
    "about.pcg.titleNormal": "More than accountants.",
    "about.pcg.titleItalic": "Partners in your progress.",
    "about.pcg.paragraph1":
      "PCG (Phangkawira Consulting Group) is a professional consulting firm providing a broad range of financial support services to companies, business owners, and specialized clients. Our consulting practice covers accounting and bookkeeping, as well as customized financial services designed to address each client's specific needs.",
    "about.pcg.paragraph2":
      "We recognize that every business operates under unique circumstances, operational needs, and financial challenges. Right from the start, our approach is centered on listening, gaining a real understanding of your business, priorities, and goals to service each client.",

    // About Vision & Mission
    "about.vision.tag": "VISION",
    "about.vision.title":
      "To be a trusted partner that brings clarity, confidence, and sustainable growth to every business we serve",
    "about.mission.tag": "MISSION",
    "about.mission.title":
      "We help businesses manage financial and operational complexity through reliable expertise, practical insight, and tailored professional support.",
    "about.mission.item1": "Deliver reliable professional services.",
    "about.mission.item2": "Bring clarity to complex information.",
    "about.mission.item3": "Assist clients to achieve financial control.",
    "about.mission.item4": "Build long-term trust relationships.",
    "about.mission.item5": "Enable sustainable business growth.",

    // About Why Choose PCG
    "about.whyChoose.tag": "WHY CHOOSE PCG",
    "about.whyChoose.titleNormal": "Support that ",
    "about.whyChoose.titleItalic": "goes beyond the numbers.",
    "about.whyChoose.description":
      "PCG helps businesses manage tax, accounting, bookkeeping, and financial needs with a structured, professional approach tailored to their goals.",
    "about.whyChoose.ctaButton": "START A CONVERSATION",
    "about.whyChoose.card1Title": "One-Stop Business Solutions",
    "about.whyChoose.card1Body":
      "Practical, integrated support across tax, accounting, advisory, and administrative processes through a single consulting partner.",
    "about.whyChoose.card2Title": "Professional Network",
    "about.whyChoose.card2Body":
      "Access specialized advisory support through our consulting network, bringing extra depth and resource when your business needs it.",
    "about.whyChoose.card3Title": "Compliance & Risk Management",
    "about.whyChoose.card3Body":
      "Structured processes to manage compliance obligations, reduce operational risk, and keep financial reporting accurate and timely.",
    "about.whyChoose.card4Title": "Strategic Business Insight",
    "about.whyChoose.card4Body":
      "Turn financial data into practical insight so management can make better-informed decisions.",

    // About People
    "about.people.tag": "THE PEOPLE BEHIND PCG",
    "about.people.titleNormal": "Built by expertise. ",
    "about.people.titleItalic": "Driven by purpose.",
    "about.people.description":
      "Meet the professionals behind PCG—bringing hands-on experience, clear focus, and a shared commitment to your success.",

    // Services Hero
    "services.hero.title": "Support designed around your business needs.",
    "services.hero.description":
      "From tax compliance and bookkeeping to financial analysis and business administration, PCG provides practical support that helps you stay organized and make informed decisions.",
    "services.hero.discoverBtn": "DISCOVER OUR SERVICES",
    "services.hero.discussBtn": "DISCUSS YOUR NEEDS",

    // Services Scope of Support
    "services.scope.tag": "SCOPE OF SUPPORT",
    "services.scope.titleItalic": "Practical support",
    "services.scope.titleNormal": "from compliance to business insight.",
    "services.scope.description":
      "Whether you need recurring compliance support, cleaner financial records, or custom advice to scale, PCG provides flexible services built around your needs.",
    "services.scope.s1Title": "Taxation",
    "services.scope.s1Desc":
      "Support your tax responsibilities with a more structured process and practical guidance.",
    "services.scope.s1Cta": "DISCUSS TAX SUPPORT",
    "services.scope.s1F1": "Tax consultation",
    "services.scope.s1F2": "Monthly and annual tax reporting",
    "services.scope.s1F3": "Tax compliance review",
    "services.scope.s1F4": "Tax planning",
    "services.scope.s1F5": "Tax audit assistance",
    "services.scope.s2Title": "Accounting & Bookkeeping",
    "services.scope.s2Desc":
      "Build reliable financial records that give your business a clearer view of its day-to-day operations.",
    "services.scope.s2Cta": "DISCUSS ACCOUNTING SUPPORT",
    "services.scope.s2F1": "Financial transaction recording",
    "services.scope.s2F2": "Monthly financial statements",
    "services.scope.s2F3": "Annual financial statements",
    "services.scope.s2F4": "Bank reconciliation",
    "services.scope.s2F5": "Accounting journal adjustments",
    "services.scope.s3Title": "Business Administration Support",
    "services.scope.s3Desc":
      "Get flexible support for administrative work that helps your business run in a more organized way.",
    "services.scope.s3Cta": "DISCUSS BUSINESS SUPPORT",
    "services.scope.s3F1": "Corporate administration",
    "services.scope.s3F2": "Document coordination",
    "services.scope.s3F3": "Administrative tasks",
    "services.scope.s3F4": "Customized support",
    "services.scope.s4Title": "Other Financial Services",
    "services.scope.s4Desc":
      "Turn financial information into useful insight for planning, control, and better decisions.",
    "services.scope.s4Cta": "DISCUSS FINANCIAL SUPPORT",
    "services.scope.s4F1": "Financial statement analysis",
    "services.scope.s4F2": "Financial projections",
    "services.scope.s4F3": "Cash-flow management",
    "services.scope.s4F4": "Corporate budgeting",
    "services.scope.s4F5": "Financial performance evaluation",

    // Services Current Need
    "services.need.tag": "START WITH YOUR CURRENT NEED",
    "services.need.titleItalic": "Where does your business need more clarity?",
    "services.need.subtitle":
      "Choose the statement below that matches your reality. We can build our service scope around where you are right now.",
    "services.need.n1Title": "Stay compliant",
    "services.need.n1Body":
      "Organize tax reportings and meet statutory requirements on time.",
    "services.need.n2Title": "Organize your books",
    "services.need.n2Body":
      "Create structured records for cleaner accounting and financial reporting.",
    "services.need.n3Title": "Understand performance",
    "services.need.n3Body":
      "Use financial information to understand what is happening in your business.",
    "services.need.n4Title": "Get strategic support",
    "services.need.n4Body":
      "Evaluate financial decisions and prepare your business for growth.",

    // Services Overrides
    "services.ourApproach.tag": "HOW WE WORK",
    "services.ourApproach.titlePrefix": "From ",
    "services.ourApproach.titleHighlight": "understanding ",
    "services.ourApproach.titleSuffix": "to confident action.",
    "services.ourApproach.subtitle":
      "Managing and growing your business with strategic financial advice starts with four clear steps.",
    "services.cta.tag": "START A CONVERSATION",
    "services.cta.titleNormal": "Not sure which service is right for you?",
    "services.cta.description":
      "Tell us about your current situation. We'll help you define the right scope of support for your business.",
    "services.cta.buttonText": "Discuss Your Needs",

    // Insights Hero
    "insight.hero.tag": "PCG FEATURED INSIGHT",
    "insight.hero.title":
      "Navigating Corporate Tax Compliance and Minimizing Business Risks",
    "insight.hero.description":
      "Discover how managing your tax obligations properly and structuring your financial administration can support your business development and drive sustainable decision-making",
    "insight.hero.author": "By PCG Editorial Team",
    "insight.hero.date": "September 10, 2026",
    "insight.hero.readTime": "5 min read",

    // Insights Section
    "insight.articles.title": "Latest Articles & Insights",
    "insight.articles.subtitle":
      "Stay informed with the latest tax regulations, accounting best practices, and strategic financial insights.",
    "insight.articles.readBtn": "Read",
    "insight.articles.sidebarTitle": "Articles Categories",

    // Insights Categories
    "insight.cat.all": "All Categories",
    "insight.cat.tax": "Taxation Services",
    "insight.cat.acc": "Accounting & Bookkeeping",
    "insight.cat.fin": "Financial Analysis",
    "insight.cat.latest": "Latest",

    // Insights Articles Data
    "insight.p1.title":
      "Effective Tax Planning Strategies for Growing Enterprises",
    "insight.p1.excerpt":
      "Discover how managing your tax obligations properly and structuring your financial administration can support your business development.",
    "insight.p2.title":
      "Building Reliable Financial Records for Day-to-Day Operations",
    "insight.p2.excerpt":
      "Learn practical steps to keep your financial statements accurate, audit-ready, and valuable for strategic decision-making.",
    "insight.p3.title":
      "Financial Analysis & Valuation Techniques for Business Growth",
    "insight.p3.excerpt":
      "Turn raw financial data into actionable corporate forecasting, budgeting, and performance evaluation metrics.",
    "insight.p4.title":
      "Corporate Tax Compliance Checklist for the Upcoming Fiscal Year",
    "insight.p4.excerpt":
      "Ensure your enterprise meets statutory tax filing requirements and avoids common compliance pitfalls.",
    "insight.p5.title":
      "Streamlining Accounting Systems and Document Coordination",
    "insight.p5.excerpt":
      "How modern cloud bookkeeping and automated document administration improve operational transparency.",
    "insight.p6.title":
      "Cash-Flow Management and Strategic Corporate Budgeting",
    "insight.p6.excerpt":
      "Key strategies for managing liquidity, optimizing operational budgets, and preparing for capital investments.",
    "insight.p7.title":
      "Understanding Transfer Pricing Regulations for Mid-Market Firms",
    "insight.p7.excerpt":
      "A comprehensive overview of compliance standards, documentation rules, and risk mitigation in transfer pricing.",
    "insight.p8.title": "Preparing Annual Financial Statements with Confidence",
    "insight.p8.excerpt":
      "Best practices for closing fiscal periods, completing bank reconciliations, and filing statutory returns.",
    "insight.p9.title":
      "Evaluating Business Valuation Methodologies for Strategic M&A",
    "insight.p9.excerpt":
      "An essential guide to discounted cash flows, earnings multiples, and asset valuation in corporate transactions.",

    // Insight Detail Content
    "insight.detail.backLink": "Back to Insights",
    "insight.detail.exploreMore": "Explore More Insights",
    "insight.detail.discussBtn": "Discuss Your Needs",
    "insight.detail.p1":
      "Managing corporate tax compliance and financial records effectively is essential for organizational stability and sustainable growth. Companies operating in competitive markets face evolving regulatory frameworks that require structured oversight and continuous monitoring.",
    "insight.detail.h1": "Strategic Tax Planning & Risk Oversight",
    "insight.detail.p2":
      "Proactive tax advisory allows businesses to structure financial transactions efficiently while mitigating legal and regulatory risks. From periodic tax reporting to statutory audits, transparent financial record-keeping builds credibility with tax authorities and financial partners.",
    "insight.detail.quote":
      '"Proper financial administration turns compliance from a routine obligation into a strategic asset for business decision-making."',
    "insight.detail.h2": "Key Takeaways for Business Leaders",
    "insight.detail.li1":
      "Establish standardized monthly financial reconciliation processes.",
    "insight.detail.li2":
      "Conduct periodic tax risk assessments prior to statutory filing deadlines.",
    "insight.detail.li3":
      "Consult with specialized advisory partners to optimize tax planning strategies.",

    // Contact Hero & Form
    "contact.hero.tag": "CONTACT US",
    "contact.hero.titleLight": "Get in touch ",
    "contact.hero.titleItalic": "with PCG.",
    "contact.hero.headerSubtitle":
      "Have a question or want to discuss your business needs?",
    "contact.hero.headerDesc":
      "Send us a message and the PCG team will help you identify a practical starting point.",
    "contact.hero.cardTitle": "Tell us what you need.",
    "contact.hero.cardDesc":
      "Share a little about your business, your current challenge, or the service you would like to discuss.",
    "contact.form.fullName": "Full Name",
    "contact.form.fullNamePlaceholder": "Enter your name",
    "contact.form.email": "Email Address",
    "contact.form.emailPlaceholder": "you@example.com",
    "contact.form.service": "Service of interest",
    "contact.form.selectOne": "Select one",
    "contact.form.optTax": "Taxation Services (Tax Box)",
    "contact.form.optAcc": "Accounting & Bookkeeping",
    "contact.form.optBiz": "Business Administration Support",
    "contact.form.optFin": "Other Financial Services",
    "contact.form.optGen": "General Inquiry",
    "contact.form.phone": "Phone number (optional)",
    "contact.form.message": "Message",
    "contact.form.messagePlaceholder": "Tells how we can help",
    "contact.form.sendBtn": "Send Message",
    "contact.form.sendingBtn": "Sending...",
    "contact.form.successMsg":
      "Thank you! Your message has been received. Our team will contact you shortly.",
    "contact.form.errName": "Full Name is required.",
    "contact.form.errEmailEmpty": "Email Address is required.",
    "contact.form.errEmailInvalid": "Please enter a valid email address.",
    "contact.form.errMsgEmpty": "Message is required.",
    "contact.form.errMsgShort": "Message must be at least 10 characters.",

    // Contact Location
    "contact.location.tag": "LOCATION",
    "contact.location.title": "Visit Our Office",
    "contact.location.address":
      "Green Lake City Boulevard No.23, Rukan Cordoba Blk. D Lt 3, Kota Tangerang, Banten",

    // Contact CTA Overrides
    "contact.cta.tag": "START A CONVERSATION",
    "contact.cta.titleItalic": "Ready to take the next step?",
    "contact.cta.description": "Discuss your business needs with PCG.",
    "contact.cta.buttonText": "Discuss Your Needs",
  },
  id: {
    "nav.home": "Beranda",
    "nav.services": "Layanan",
    "nav.insights": "Wawasan",
    "nav.about": "Tentang Kami",
    "nav.contact": "Kontak",
    "nav.discussNeeds": "Diskusikan Kebutuhanmu",
    "cta.tag": "MULAI DISKUSI",
    "cta.titleNormal": "Siap membawa kejelasan untuk bisnis Anda?",
    "cta.titleItalic": "Mari ambil langkah selanjutnya",
    "cta.description":
      "Ceritakan kebutuhan pajak, akuntansi, maupun keuangan Anda kepada kami. PCG siap membantu Anda menemukan langkah awal yang paling tepat untuk bisnis Anda.",
    "cta.buttonText": "Diskusikan Kebutuhanmu",
    "footer.tagline":
      "Dukungan profesional untuk urusan pajak, akuntansi, dan layanan keuangan. Kami hadir untuk membantu bisnismu melangkah maju dengan penuh keyakinan.",
    "footer.rights": "Hak Cipta Dilindungi",
    "footer.quickLinks": "Tautan Cepat",
    "footer.services": "Layanan Kami",
    "footer.contactUs": "Hubungi Kami",

    // Home Hero
    "home.hero.title1": "PAHAMI KEUANGAN ANDA,",
    "home.hero.titleHighlight": "AMBIL KEPUTUSAN BISNIS",
    "home.hero.title2": "DENGAN LEBIH YAKIN.",
    "home.hero.description":
      "PCG hadir untuk membantu bisnis Anda mengelola pajak, pembukuan, dan urusan finansial lainnya. Kami bekerja dengan rapi dan profesional, merancang strategi yang benar-benar pas dengan tujuan bisnis Anda.",
    "home.hero.exploreBtn": "PELAJARI LAYANAN KAMI",
    "home.hero.discussBtn": "DISKUSIKAN KEBUTUHANMU",

    // Home About PCG
    "home.about.tag": "TENTANG PCG",
    "home.about.titleNormal": "Lebih dari sekadar akuntan.",
    "home.about.titleItalic": "Kami Mitra Kesuksesan Anda.",
    "home.about.description":
      "Kami hadir untuk mendampingi para pelaku bisnis, keluarga, maupun individu yang ingin terus maju agar dapat mengambil keputusan finansial dengan penuh keyakinan. Kami menyederhanakan informasi yang rumit menjadi langkah-langkah praktis yang jelas dan siap merencanakan masa depan dengan lebih matang..",
    "home.about.button": "CARI TAHU LEBIH LANJUT",

    // Home Stats
    "home.stats.tag": "SEKILAS TENTANG PCG",
    "home.stats.titleItalic": "Membangun fondasi yang rapi ",
    "home.stats.titleNormal": "untuk keputusan bisnis yang lebih tepat.",
    "home.stats.description":
      " Fakta nyata tentang cara kerja dan pendekatan kami disampaikan apa adanya, tanpa klaim atau janji yang berlebihan.",
    "home.stats.stat1": "Layanan utama",
    "home.stats.stat2": "Tahapan pendekatan kami",
    "home.stats.stat3": "Mitra pendukung terintegrasi",
    "home.stats.stat4": "Klien & Pelanggan",

    // Home How We Help
    "home.howWeHelp.tag": "BAGAIMANA KAMI MEMBANTU",
    "home.howWeHelp.titleItalic": "Bisnis Anda Berhak",
    "home.howWeHelp.titleNormal":
      "Mendapatkan Lebih dari Sekadar Pembukuan Biasa.",
    "home.howWeHelp.description":
      "Kami hadir membantu bisnis Anda mengelola pajak, akuntansi, pembukuan, hingga laporan keuangan dengan rapi dan transparan. Dengan begitu, Anda bisa mengambil keputusan dengan lebih yakin dan kembali fokus pada pertumbuhan bisnis.",
    "home.howWeHelp.card1Title": "Penuhi Kewajiban Pajak dengan Tenang.",
    "home.howWeHelp.card1Body":
      "Mulai dari urusan lapor pajak, tinjauan, perencanaan, hingga pendampingan audit—semuanya kami tangani dengan teliti dan transparan.",
    "home.howWeHelp.card2Title": "Bangun Fondasi Keuangan yang Lebih Kokoh.",
    "home.howWeHelp.card2Body":
      "Kami merapikan pembukuan, laporan keuangan, rekonsiliasi bank, hingga penyesuaian jurnal, agar data finansial bisnis Anda selalu akurat dan teratur.",
    "home.howWeHelp.card3Title": "Wawasan Cerdas untuk Keputusan Tepat.",
    "home.howWeHelp.card3Body":
      "Dapatkan analisis keuangan, proyeksi arus kas, penyusunan anggaran, dan evaluasi performa yang akan membantu Anda merencanakan langkah bisnis ke depan dengan lebih matang.",

    // Home Why Choose Us
    "home.whyChooseUs.tag": "Mengapa Banyak Bisnis Memilih Kami",
    "home.whyChooseUs.titleItalic": "Dukungan yang tepat",
    "home.whyChooseUs.titleNormal":
      "akan mengubah cara bisnis Anda melangkah maju.",
    "home.whyChooseUs.description":
      "Mengelola bisnis tentu butuh lebih dari sekadar pembukuan yang rapi. Anda membutuhkan informasi yang jernih, panduan yang praktis, serta mitra tepercaya yang mampu melihat gambaran besar bisnis Anda secara utuh.",
    "home.whyChooseUs.card1Title": "Konsultasi Pajak",
    "home.whyChooseUs.card1Body":
      "Kelola kewajiban dengan baik, kenali risikonya sejak dini, dan ambil keputusan perpajakan dengan lebih bijak.",
    "home.whyChooseUs.card2Title": "Keuangan & Konsultasi",
    "home.whyChooseUs.card2Body":
      "Manfaatkan transparansi data keuangan Anda untuk merencanakan masa depan dan menciptakan pertumbuhan bisnis yang berkelanjutan.",

    // Home Our Approach
    "home.ourApproach.tag": "PENDEKATAN KAMI",
    "home.ourApproach.titlePrefix": "Dari ",
    "home.ourApproach.titleHighlight": "Kejelasan Keuangan",
    "home.ourApproach.titleSuffix": "Menuju Langkah Pasti.",
    "home.ourApproach.subtitle":
      "Pendekatan terstruktur untuk membantu bisnis Anda tetap patuh, terorganisir, dan siap menghadapi masa depan.",
    "home.ourApproach.step1Title": "Memahami Bisnis Anda",
    "home.ourApproach.step1Body":
      "Kami mengevaluasi posisi keuangan, catatan, dan proses administratif Anda untuk memahami kondisi bisnis Anda saat ini.",
    "home.ourApproach.step2Title": "Mengidentifikasi Prioritas",
    "home.ourApproach.step2Body":
      "Kami mengidentifikasi risiko, celah, dan prioritas, lalu berfokus pada area yang membutuhkan perhatian terlebih dahulu.",
    "home.ourApproach.step3Title": "Menerapkan Solusi",
    "home.ourApproach.step3Body":
      "Kami menerapkan solusi perpajakan, akuntansi, dan keuangan yang disepakati sesuai dengan kebutuhan bisnis Anda.",
    "home.ourApproach.step4Title": "Tinjauan & Langkah Maju",
    "home.ourApproach.step4Body":
      "Kami meninjau kemajuan secara berkala dan memberikan dukungan berkelanjutan seiring berkembangnya bisnis Anda.",

    // Home Insights
    "home.insights.tag": "ARTIKEL & WAWASAN",
    "home.insights.titleNormal": "Wawasan praktis untuk",
    "home.insights.titleItalic": "keputusan bisnis yang lebih baik.",
    "home.insights.subtitle":
      "Perspektif yang jelas tentang perpajakan, akuntansi, dan manajemen keuangan untuk bisnis yang ingin melangkah maju dengan yakin.",
    "home.insights.excerpt":
      "Perspektif yang jelas tentang perpajakan, akuntansi, dan manajemen keuangan untuk bisnis yang ingin melangkah maju dengan yakin.",
    "home.insights.viewDetails": "Lihat Detail",
    "home.insights.post1Title":
      "Apa yang Sebenarnya Disampaikan oleh Laporan Keuangan Anda",
    "home.insights.post2Title": "Mengapa Pelaporan Keuangan Bulanan Penting",
    "home.insights.post3Title":
      "Cara Praktis Meningkatkan Visibilitas Arus Kas",
    "home.insights.cat1": "Manajemen Keuangan",
    "home.insights.cat2": "Akuntansi",
    "home.insights.cat3": "Keuangan",

    // Home FAQ
    "home.faq.tag": "PERTANYAAN YANG SERING DIAJUKAN",
    "home.faq.titlePrefix": "Temukan jawaban ",
    "home.faq.titleHighlight": "untuk langkah Anda selanjutnya.",
    "home.faq.subtitle":
      "Sebelum memulai percakapan, berikut adalah jawaban atas pertanyaan yang sering diajukan bisnis mengenai layanan, proses, dan kerja sama dengan PCG.",
    "home.faq.q1": "Layanan apa saja yang disediakan oleh PCG?",
    "home.faq.a1":
      "PCG menyediakan dukungan konsultasi profesional di bidang perpajakan, akuntansi & pembukuan, serta layanan keuangan kustom lainnya. Cakupan layanan disesuaikan dengan kebutuhan bisnis Anda dan akan didiskusikan sebelum kerja sama dimulai.",
    "home.faq.q2": "Bagaimana cara kerja sama dengan PCG?",
    "home.faq.a2":
      "Kami memulai dengan konsultasi awal untuk mengevaluasi struktur bisnis, catatan, dan tantangan utama Anda. Dari sana, kami mengusulkan cakupan kerja kustom yang disesuaikan dengan tujuan dan skala operasional Anda.",
    "home.faq.q3":
      "Informasi apa yang perlu disiapkan sebelum menghubungi PCG?",
    "home.faq.a3":
      "Menyiapkan informasi dasar—seperti struktur perusahaan, sistem akuntansi saat ini, laporan keuangan terbaru, atau dokumen pajak—membantu kami memahami situasi Anda lebih cepat, meskipun Anda tetap bisa menghubungi kami sejak tahap awal.",
    "home.faq.q4":
      "Apakah PCG mendukung layanan bulanan berkelanjutan atau hanya proyek sekali jalan?",
    "home.faq.a4":
      "Kami mendukung kontrak bulanan berkelanjutan (seperti pembukuan bulanan atau pelaporan pajak) maupun layanan konsultasi berbasis proyek sesuai dengan kebutuhan perusahaan Anda.",
    "home.faq.q5": "Bagaimana penentuan harga layanan PCG?",
    "home.faq.a5":
      "Harga disesuaikan berdasarkan cakupan, kompleksitas, dan volume transaksi operasional bisnis Anda. Kami memberikan proposal cakupan dan harga yang transparan setelah diskusi awal.",

    // About Hero
    "about.hero.title1": "Lebih dari sekadar akuntan.",
    "about.hero.title2": "Mitra dalam kemajuan Anda.",
    "about.hero.description":
      "PCG membantu bisnis mengambil keputusan keuangan dengan yakin. Mulai dari perpajakan dan akuntansi hingga perencanaan keuangan dan dukungan bisnis, kami mengubah informasi kompleks menjadi langkah nyata yang jelas dan praktis.",
    "about.hero.discoverBtn": "TEMUKAN PENDEKATAN KAMI",
    "about.hero.teamBtn": "LIHAT TIM KAMI",

    // About PCG Section
    "about.pcg.tag": "TENTANG PCG",
    "about.pcg.titleNormal": "Lebih dari sekadar akuntan.",
    "about.pcg.titleItalic": "Mitra dalam kemajuan Anda.",
    "about.pcg.paragraph1":
      "PCG (Phangkawira Consulting Group) adalah perusahaan konsultan profesional yang menyediakan berbagai layanan dukungan keuangan untuk perusahaan, pemilik bisnis, dan klien spesialis. Praktik konsultasi kami mencakup akuntansi dan pembukuan, serta layanan keuangan kustom yang dirancang untuk menjawab kebutuhan spesifik setiap klien.",
    "about.pcg.paragraph2":
      "Kami menyadari bahwa setiap bisnis beroperasi dalam kondisi, kebutuhan operasional, dan tantangan keuangan yang unik. Sejak awal, pendekatan kami berpusat pada mendengarkan serta memahami bisnis, prioritas, dan tujuan Anda secara mendalam.",

    // About Vision & Mission
    "about.vision.tag": "VISI",
    "about.vision.title":
      "Menjadi mitra terpercaya yang menghadirkan kejelasan, rasa percaya diri, dan pertumbuhan berkelanjutan bagi setiap bisnis yang kami layani",
    "about.mission.tag": "MISI",
    "about.mission.title":
      "Kami membantu bisnis mengelola kompleksitas keuangan dan operasional melalui keahlian terpercaya, wawasan praktis, dan dukungan profesional yang disesuaikan.",
    "about.mission.item1": "Memberikan layanan profesional yang terpercaya.",
    "about.mission.item2":
      "Menghadirkan kejelasan pada informasi yang kompleks.",
    "about.mission.item3":
      "Membantu klien mencapai kendali penuh atas keuangan.",
    "about.mission.item4": "Membangun hubungan kepercayaan jangka panjang.",
    "about.mission.item5": "Mendorong pertumbuhan bisnis yang berkelanjutan.",

    // About Why Choose PCG
    "about.whyChoose.tag": "MENGAPA MEMILIH PCG",
    "about.whyChoose.titleNormal": "Dukungan yang ",
    "about.whyChoose.titleItalic": "melampaui sekadar angka.",
    "about.whyChoose.description":
      "PCG membantu bisnis mengelola perpajakan, akuntansi, pembukuan, dan kebutuhan keuangan dengan pendekatan terstruktur dan profesional yang disesuaikan dengan tujuan Anda.",
    "about.whyChoose.ctaButton": "MULAI PERCAKAPAN",
    "about.whyChoose.card1Title": "Solusi Bisnis Satu Pintu",
    "about.whyChoose.card1Body":
      "Dukungan praktis dan terintegrasi di bidang pajak, akuntansi, konsultasi, dan proses administratif melalui satu mitra konsultan.",
    "about.whyChoose.card2Title": "Jaringan Profesional",
    "about.whyChoose.card2Body":
      "Akses dukungan konsultasi spesialis melalui jaringan profesional kami yang memberikan kedalaman sumber daya saat bisnis Anda membutuhkannya.",
    "about.whyChoose.card3Title": "Kepatuhan & Manajemen Risiko",
    "about.whyChoose.card3Body":
      "Proses terstruktur untuk mengelola kewajiban kepatuhan, mengurangi risiko operasional, dan menjaga pelaporan keuangan tetap akurat serta tepat waktu.",
    "about.whyChoose.card4Title": "Wawasan Bisnis Strategis",
    "about.whyChoose.card4Body":
      "Mengubah data keuangan menjadi wawasan praktis sehingga manajemen dapat mengambil keputusan yang lebih tepat.",

    // About People
    "about.people.tag": "TIM DI BALIK PCG",
    "about.people.titleNormal": "Dibangun oleh keahlian. ",
    "about.people.titleItalic": "Didorong oleh tujuan.",
    "about.people.description":
      "Temui para profesional di balik PCG—yang membawa pengalaman langsung, fokus yang jelas, dan komitmen bersama untuk kesuksesan Anda.",

    // Services Hero
    "services.hero.title":
      "Dukungan yang dirancang khusus untuk kebutuhan bisnis Anda.",
    "services.hero.description":
      "Mulai dari kepatuhan pajak dan pembukuan hingga analisis keuangan serta administrasi bisnis, PCG hadir memberikan dukungan praktis agar bisnis Anda tetap tertata rapi dan mampu mengambil keputusan dengan cerdas.",
    "services.hero.discoverBtn": "PELAJARI LAYANAN KAMI",
    "services.hero.discussBtn": "DISKUASIKAN KEBUTUHAN ANDA",

    // Services Scope of Support
    "services.scope.tag": "RUANG LINGKUP LAYANAN",
    "services.scope.titleItalic": "Dukungan praktis, ",
    "services.scope.titleNormal":
      "mulai dari urusan kepatuhan hingga wawasan bisnis strategis",
    "services.scope.description":
      "Layanan kami didesain agar selaras dengan cara kerja bisnis Anda. Mari mulai dari area yang paling membutuhkan perhatian saat ini, lalu kita rumuskan ruang lingkup yang paling tepat bersama PCG.",
    "services.scope.s1Title": "Perpajakan",
    "services.scope.s1Desc":
      "Kami mendampingi pemenuhan kewajiban pajak Anda melalui proses yang lebih terstruktur dan panduan yang mudah diterapkan.",
    "services.scope.s1Cta": "DISKUSIKAN LAYANAN PAJAK",
    "services.scope.s1F1": "Konsultasi perpajakan",
    "services.scope.s1F2": "Pelaporan pajak bulanan dan tahunan",
    "services.scope.s1F3": "Peninjauan kepatuhan pajak",
    "services.scope.s1F4": "Perencanaan pajak",
    "services.scope.s1F5": "Pendampingan pemeriksaan pajak",
    "services.scope.s2Title": "Akuntansi & Pembukuan",
    "services.scope.s2Desc":
      "Bangun catatan keuangan terpercaya yang memberikan gambaran lebih jelas tentang operasional harian bisnis Anda.",
    "services.scope.s2Cta": "DISKUASIKAN DUKUNGAN AKUNTANSI",
    "services.scope.s2F1": "Pencatatan transaksi keuangan",
    "services.scope.s2F2": "Laporan keuangan bulanan",
    "services.scope.s2F3": "Laporan keuangan tahunan",
    "services.scope.s2F4": "Rekonsiliasi bank",
    "services.scope.s2F5": "Penyesuaian jurnal akuntansi",
    "services.scope.s3Title": "Dukungan Administrasi Bisnis",
    "services.scope.s3Desc":
      "Dapatkan dukungan fleksibel untuk pekerjaan administratif yang membantu bisnis Anda berjalan lebih terorganisir.",
    "services.scope.s3Cta": "DISKUASIKAN DUKUNGAN BISNIS",
    "services.scope.s3F1": "Administrasi perusahaan",
    "services.scope.s3F2": "Koordinasi dokumen",
    "services.scope.s3F3": "Tugas-tugas administratif",
    "services.scope.s3F4": "Dukungan kustom",
    "services.scope.s4Title": "Layanan Keuangan Lainnya",
    "services.scope.s4Desc":
      "Ubah informasi keuangan menjadi wawasan berguna untuk perencanaan, pengendalian, dan keputusan yang lebih baik.",
    "services.scope.s4Cta": "DISKUASIKAN DUKUNGAN KEUANGAN",
    "services.scope.s4F1": "Analisis laporan keuangan",
    "services.scope.s4F2": "Proyeksi keuangan",
    "services.scope.s4F3": "Manajemen arus kas",
    "services.scope.s4F4": "Penganggaran perusahaan",
    "services.scope.s4F5": "Evaluasi kinerja keuangan",

    // Services Current Need
    "services.need.tag": "MULAI DARI KEBUTUHAN ANDA SAAT INI",
    "services.need.titleItalic":
      "Di mana bisnis Anda membutuhkan kejelasan lebih?",
    "services.need.subtitle":
      "Pilih pernyataan di bawah ini yang sesuai dengan kondisi Anda. Kami dapat merancang cakupan layanan sesuai keberadaan bisnis Anda saat ini.",
    "services.need.n1Title": "Tetap patuh",
    "services.need.n1Body":
      "Rapikan pelaporan pajak dan penuhi persyaratan regulasi tepat waktu.",
    "services.need.n2Title": "Merapikan pembukuan",
    "services.need.n2Body":
      "Buat catatan terstruktur untuk akuntansi dan pelaporan keuangan yang lebih rapi.",
    "services.need.n3Title": "Memahami kinerja",
    "services.need.n3Body":
      "Gunakan informasi keuangan untuk memahami apa yang terjadi dalam bisnis Anda.",
    "services.need.n4Title": "Dapatkan dukungan strategis",
    "services.need.n4Body":
      "Evaluasi keputusan keuangan dan persiapkan bisnis Anda untuk pertumbuhan.",

    // Services Overrides
    "services.ourApproach.tag": "CARA KERJA KAMI",
    "services.ourApproach.titlePrefix": "Dari ",
    "services.ourApproach.titleHighlight": "pemahaman ",
    "services.ourApproach.titleSuffix": "menuju langkah pasti.",
    "services.ourApproach.subtitle":
      "Mengelola dan mengembangkan bisnis Anda dengan nasihat keuangan strategis dimulai dari empat langkah jelas.",
    "services.cta.tag": "MULAI PERCAKAPAN",
    "services.cta.titleNormal":
      "Belum yakin layanan mana yang tepat untuk Anda?",
    "services.cta.description":
      "Beritahu kami tentang kondisi Anda saat ini. Kami akan membantu menentukan cakupan dukungan yang tepat untuk bisnis Anda.",
    "services.cta.buttonText": "Diskusikan Kebutuhan Anda",

    // Insights Hero
    "insight.hero.tag": "WAWASAN UNGGULAN PCG",
    "insight.hero.title":
      "Navigasi Kepatuhan Pajak Perusahaan dan Meminimalkan Risiko Bisnis",
    "insight.hero.description":
      "Temukan bagaimana mengelola kewajiban perpajakan dengan tepat dan menata administrasi keuangan dapat mendukung pengembangan bisnis serta mendorong pengambilan keputusan yang berkelanjutan.",
    "insight.hero.author": "Oleh Tim Redaksi PCG",
    "insight.hero.date": "10 September 2026",
    "insight.hero.readTime": "Waktu baca 5 mnt",

    // Insights Section
    "insight.articles.title": "Artikel & Wawasan Terbaru",
    "insight.articles.subtitle":
      "Dapatkan informasi terbaru mengenai regulasi perpajakan, praktik terbaik akuntansi, dan wawasan keuangan strategis.",
    "insight.articles.readBtn": "Baca",
    "insight.articles.sidebarTitle": "Kategori Artikel",

    // Insights Categories
    "insight.cat.all": "Semua Kategori",
    "insight.cat.tax": "Layanan Perpajakan",
    "insight.cat.acc": "Akuntansi & Pembukuan",
    "insight.cat.fin": "Analisis Keuangan",
    "insight.cat.latest": "Terbaru",

    // Insights Articles Data
    "insight.p1.title":
      "Strategi Perencanaan Pajak Efektif untuk Perusahaan Berkembang",
    "insight.p1.excerpt":
      "Temukan bagaimana mengelola kewajiban pajak dengan tepat dan menata administrasi keuangan dapat mendukung pengembangan bisnis Anda.",
    "insight.p2.title":
      "Membangun Catatan Keuangan Terpercaya untuk Operasional Harian",
    "insight.p2.excerpt":
      "Pelajari langkah-langkah praktis untuk menjaga laporan keuangan tetap akurat, siap audit, dan bernilai bagi pengambilan keputusan strategis.",
    "insight.p3.title":
      "Teknik Analisis & Valuasi Keuangan untuk Pertumbuhan Bisnis",
    "insight.p3.excerpt":
      "Ubah data keuangan mentah menjadi proyeksi perusahaan, penganggaran, dan metrik evaluasi kinerja yang dapat ditindaklanjuti.",
    "insight.p4.title":
      "Daftar Periksa Kepatuhan Pajak Perusahaan untuk Tahun Fiskal Mendatang",
    "insight.p4.excerpt":
      "Pastikan perusahaan Anda memenuhi persyaratan pelaporan pajak regulasi dan menghindari kendala kepatuhan umum.",
    "insight.p5.title":
      "Meningkatkan Efisiensi Sistem Akuntansi dan Koordinasi Dokumen",
    "insight.p5.excerpt":
      "Bagaimana pembukuan berbasis cloud dan administrasi dokumen otomatis meningkatkan transparansi operasional.",
    "insight.p6.title":
      "Manajemen Arus Kas dan Penganggaran Perusahaan Strategis",
    "insight.p6.excerpt":
      "Strategi utama untuk mengelola likuiditas, mengoptimalkan anggaran operasional, dan mempersiapkan investasi modal.",
    "insight.p7.title":
      "Memahami Regulasi Penetapan Harga Transfer (Transfer Pricing) untuk Perusahaan Menengah",
    "insight.p7.excerpt":
      "Gambaran menyeluruh tentang standar kepatuhan, aturan dokumentasi, dan mitigasi risiko dalam transfer pricing.",
    "insight.p8.title":
      "Menyiapkan Laporan Keuangan Tahunan dengan Percaya Diri",
    "insight.p8.excerpt":
      "Praktik terbaik untuk penutupan periode fiskal, penyelesaian rekonsiliasi bank, dan pelaporan pajak tahunan.",
    "insight.p9.title":
      "Mengevaluasi Metodologi Valuasi Bisnis untuk M&A Strategis",
    "insight.p9.excerpt":
      "Panduan penting mengenai arus kas terdiskonto, kelipatan laba, dan valuasi aset dalam transaksi perusahaan.",

    // Insight Detail Content
    "insight.detail.backLink": "Kembali ke Wawasan",
    "insight.detail.exploreMore": "Jelajahi Wawasan Lainnya",
    "insight.detail.discussBtn": "Diskusikan Kebutuhan Anda",
    "insight.detail.p1":
      "Mengelola kepatuhan pajak perusahaan dan catatan keuangan secara efektif sangat penting untuk stabilitas organisasi dan pertumbuhan yang berkelanjutan. Perusahaan yang beroperasi di pasar kompetitif menghadapi kerangka regulasi yang terus berkembang yang memerlukan pengawasan terstruktur dan pemantauan berkesinambungan.",
    "insight.detail.h1": "Perencanaan Pajak Strategis & Pengawasan Risiko",
    "insight.detail.p2":
      "Konsultasi pajak yang proaktif memungkinkan bisnis menata transaksi keuangan secara efisien sekaligus meminimalkan risiko hukum dan regulasi. Dari pelaporan pajak berkala hingga pemeriksaan resmi, pencatatan keuangan yang transparan membangun kredibilitas dengan otoritas pajak dan mitra keuangan.",
    "insight.detail.quote":
      '"Administrasi keuangan yang tepat mengubah kepatuhan dari sekadar kewajiban rutin menjadi aset strategis dalam pengambilan keputusan bisnis."',
    "insight.detail.h2": "Poin Kunci Bagi Pemimpin Bisnis",
    "insight.detail.li1":
      "Membangun proses rekonsiliasi keuangan bulanan yang terstandarisasi.",
    "insight.detail.li2":
      "Melakukan evaluasi risiko pajak secara berkala sebelum batas waktu pelaporan resmi.",
    "insight.detail.li3":
      "Berkonsultasi dengan mitra ahli untuk mengoptimalkan strategi perencanaan pajak.",

    // Contact Hero & Form
    "contact.hero.tag": "HUBUNGI KAMI",
    "contact.hero.titleLight": "Hubungi ",
    "contact.hero.titleItalic": "PCG.",
    "contact.hero.headerSubtitle":
      "Punya pertanyaan atau ingin mendiskusikan kebutuhan bisnis Anda?",
    "contact.hero.headerDesc":
      "Kirimkan pesan kepada kami dan tim PCG akan membantu Anda menentukan titik awal yang praktis.",
    "contact.hero.cardTitle": "Beritahu kami kebutuhan Anda.",
    "contact.hero.cardDesc":
      "Bagikan sedikit informasi mengenai bisnis Anda, tantangan yang dihadapi, atau layanan yang ingin didiskusikan.",
    "contact.form.fullName": "Nama Lengkap",
    "contact.form.fullNamePlaceholder": "Masukkan nama Anda",
    "contact.form.email": "Alamat Email",
    "contact.form.emailPlaceholder": "nama@contoh.com",
    "contact.form.service": "Layanan yang diminati",
    "contact.form.selectOne": "Pilih salah satu",
    "contact.form.optTax": "Layanan Perpajakan (Tax Box)",
    "contact.form.optAcc": "Akuntansi & Pembukuan",
    "contact.form.optBiz": "Dukungan Administrasi Bisnis",
    "contact.form.optFin": "Layanan Keuangan Lainnya",
    "contact.form.optGen": "Pertanyaan Umum",
    "contact.form.phone": "Nomor Telepon (opsional)",
    "contact.form.message": "Pesan",
    "contact.form.messagePlaceholder": "Tuliskan bagaimana kami dapat membantu",
    "contact.form.sendBtn": "Kirim Pesan",
    "contact.form.sendingBtn": "Mengirim...",
    "contact.form.successMsg":
      "Terima kasih! Pesan Anda telah diterima. Tim kami akan segera menghubungi Anda.",
    "contact.form.errName": "Nama Lengkap wajib diisi.",
    "contact.form.errEmailEmpty": "Alamat Email wajib diisi.",
    "contact.form.errEmailInvalid": "Masukkan alamat email yang valid.",
    "contact.form.errMsgEmpty": "Pesan wajib diisi.",
    "contact.form.errMsgShort": "Pesan minimal 10 karakter.",

    // Contact Location
    "contact.location.tag": "LOKASI",
    "contact.location.title": "Kunjungi Kantor Kami",
    "contact.location.address":
      "Green Lake City Boulevard No.23, Rukan Cordoba Blk. D Lt 3, Kota Tangerang, Banten",

    // Contact CTA Overrides
    "contact.cta.tag": "MULAI PERCAKAPAN",
    "contact.cta.titleItalic": "Siap mengambil langkah berikutnya?",
    "contact.cta.description": "Diskusikan kebutuhan bisnis Anda dengan PCG.",
    "contact.cta.buttonText": "Diskusikan Kebutuhan Anda",
  },
};

const fallbackState: Record<string, Ref<any>> = {};

function getLocaleState<T>(key: string, init: () => T): Ref<T> {
  try {
    if (typeof useState === "function") {
      return useState<T>(key, init);
    }
  } catch {
    // ignore if useState is unavailable in unit test context
  }
  let state = fallbackState[key];
  if (!state) {
    state = ref(init());
    fallbackState[key] = state;
  }
  return state as Ref<T>;
}

export function useLanguage() {
  const currentLocale = getLocaleState<Locale>("currentLocale", () => "en");

  const currentLanguage = computed(() => {
    return (
      LANGUAGES.find((l) => l.code === currentLocale.value) || LANGUAGES[0]
    );
  });

  function setLanguage(locale: Locale) {
    currentLocale.value = locale;
  }

  function toggleLanguage() {
    currentLocale.value = currentLocale.value === "en" ? "id" : "en";
  }

  function t(key: string, fallback?: string): string {
    const dict = translations[currentLocale.value] || translations.en;
    return dict[key] ?? translations.en[key] ?? fallback ?? key;
  }

  return {
    currentLocale,
    currentLanguage,
    setLanguage,
    toggleLanguage,
    t,
    languages: LANGUAGES,
  };
}
