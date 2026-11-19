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
    flag: "/images/redesign/flag-uk.png",
  },
  {
    code: "id",
    label: "Bahasa Indonesia",
    shortLabel: "ID",
    flag: "/images/redesign/flag-id.png",
  },
];

const translations: Record<Locale, Record<string, string>> = {
  en: {
    "nav.home": "Home",
    "nav.services": "Services",
    "nav.insights": "Insights",
    "nav.about": "About",
    "nav.contact": "Contact",
    "nav.consultation": "Consultation",
    "nav.discussNeeds": "Discuss Your Needs",
    "cta.tag": "START A CONVERSATION",
    "cta.titleNormal": "Ready to bring more clarity to your business?",
    "cta.titleItalic": "Let's take the next step together.",
    "cta.description":
      "Tell us about your tax, accounting, or financial needs. AP Consulting will help you identify a practical starting point for your business.",
    "cta.buttonText": "Discuss Your Needs",
    "footer.tagline":
      "Professional support in tax, accounting, and financial services. We're helping businesses move forward with clarity and confidence.",
    "footer.rights": "All Rights Reserved",
    "footer.quickLinks": "Quick Links",
    "footer.services": "Services",
    "footer.contactUs": "Contact Us",
    "footer.headOffice": "Head Office",
    "footer.branchOffice": "Branch Office",
    "footer.licensedConsultants": "Licensed tax consultants",
    "footer.ikpiMember": "Member of Ikatan Konsultan Pajak Indonesia (IKPI)",
    "footer.practiceLicensePrefix": "Practice License No.",
    "footer.viewLicenses": "View licenses",
    "footer.copiedLicense": "Name copied! Opening SIKOP...",
    "footer.language": "Language:",

    // Redesign Home - Hero
    "home.v3.hero.eyebrow": "AP CONSULTING · TAX & CONSULTING SERVICES",
    "home.v3.hero.titleBold": "Clearer taxes.",
    "home.v3.hero.titleMiddle": "Move your business",
    "home.v3.hero.titleItalic": "forward confidently.",
    "home.v3.hero.subtitle":
      "Professional tax and accounting support, so you can focus on growing your business.",
    "home.v3.hero.cta": "Discuss your needs",
    "home.v3.hero.banner": "INTEGRITY. EXCELLENCE. TEAMWORK.",
    "home.v3.hero.stat1Label": "Years of tax & accounting experience",
    "home.v3.hero.stat2Label": "Active corporate & individual clients",
    "home.v3.hero.stat3Label": "Tax returns (SPT) handled every year",
    "home.v3.hero.stat4Label": "Certified tax consultants",

    // Redesign Home - Services (Our Expertise)
    "home.v3.services.eyebrow": "OUR EXPERTISE",
    "home.v3.services.titleBold": "The right support",
    "home.v3.services.titleItalic": "for your business.",
    "home.v3.services.explore": "Explore our services",
    "home.v3.services.learnMore": "Learn more",
    "home.v3.services.s1Title": "Tax Services",
    "home.v3.services.s1Desc":
      "Comprehensive tax consulting and administration: Tax Compliance, Tax Review, and Tax Audit Assistance.",
    "home.v3.services.s2Title": "Payroll Outsourcing Services",
    "home.v3.services.s2Desc":
      "Meticulous, confidential, and timely payroll and tax management, including HR administration, BPJS, and e-Payslips.",
    "home.v3.services.s3Title": "Accounting Services",
    "home.v3.services.s3Desc":
      "Sound bookkeeping, financial statement preparation (PT/CV/UMKM), accounting reviews, internal audits, and software consultation.",

    // Redesign Home - Sectors (Industries We Serve)
    "home.v3.sectors.eyebrow": "INDUSTRIES WE SERVE",
    "home.v3.sectors.titleNormal": "Experience across",
    "home.v3.sectors.titleItalic": "your industry.",
    "home.v3.sectors.description":
      "We support businesses of every size, from local SMEs to foreign-invested companies, across these sectors.",
    "home.v3.sectors.confidentialBold": "Your data stays confidential.",
    "home.v3.sectors.confidentialBody":
      "We never publish client names. Every engagement is handled under strict confidentiality.",
    "home.v3.sectors.item1": "Manufacturing",
    "home.v3.sectors.item2": "Trading & Distribution",
    "home.v3.sectors.item3": "Property & Construction",
    "home.v3.sectors.item4": "Food & Beverage",
    "home.v3.sectors.item5": "Technology & Startups",
    "home.v3.sectors.item6": "Foreign-invested (PT PMA)",

    // Redesign Home - About
    "home.v3.about.eyebrow": "ABOUT AP CONSULTING",
    "home.v3.about.titleBold": "Tax expertise.",
    "home.v3.about.titleMiddle": "A partnership",
    "home.v3.about.titleItalic": "you can rely on.",
    "home.v3.about.p1":
      "AP Consulting is backed by an experienced, high-integrity, and professional taxation team that observes current, up-to-date laws and regulations in assisting our clients — both corporate entities and individual business owners — to fulfill their tax obligations effectively.",
    "home.v3.about.p2":
      "Our commitment is to be the expert and trustworthy business partner in taking care of our clients’ taxation and accounting challenges. Through solid teamwork, optimal client service, and concrete problem-solving, we help your business move forward with clarity.",
    "home.v3.about.cta": "Get to know AP Consulting",

    // Redesign Home - Values
    "home.v3.values.eyebrow": "THE VALUES WE STAND FOR",
    "home.v3.values.titleBold": "Beyond numbers.",
    "home.v3.values.titleMiddle": "It’s about",
    "home.v3.values.titleItalic": "trust.",
    "home.v3.values.quote":
      "“If you fail to plan, you plan to fail — Plan your tax with us.”",
    "home.v3.values.v1Title": "Integrity",
    "home.v3.values.v1Desc":
      "We are always disciplined and consistent in our services to achieve company objectives.",
    "home.v3.values.v2Title": "Excellence",
    "home.v3.values.v2Desc":
      "We always strive to provide you with consistently top-notch services through our excellent staff.",
    "home.v3.values.v3Title": "Teamwork",
    "home.v3.values.v3Desc":
      "We believe teamwork is indispensable for enhancing team motivation and expertise in finding the best solutions.",

    // Redesign Home - Approach
    "home.v3.approach.eyebrow": "OUR APPROACH",
    "home.v3.approach.titleBold": "How we work",
    "home.v3.approach.titleItalic": "with your business.",
    "home.v3.approach.subtitle":
      "A clear, four-stage professional advisory framework designed to keep your tax, payroll, and financial operations compliant and growth-ready.",
    "home.v3.approach.s1Title": "Understand Your Business",
    "home.v3.approach.s1Desc":
      "We thoroughly review your business entity, transaction flows, and compliance status before recommending any action.",
    "home.v3.approach.s2Title": "Tailored Solutions",
    "home.v3.approach.s2Desc":
      "Every business has unique requirements. We craft practical strategies suited to your tax, payroll, or accounting scope — with no unnecessary complexity.",
    "home.v3.approach.s3Title": "Clear Communication",
    "home.v3.approach.s3Desc":
      "We explain regulations, tax calculations, and financial reports in plain, actionable language so you can make confident decisions.",
    "home.v3.approach.s4Title": "Ongoing Support",
    "home.v3.approach.s4Desc":
      "Compliance and financial management are ongoing priorities. We stand beside you throughout the year to anticipate deadlines and support your business growth.",

    // Redesign Home - Insights
    "home.v3.insights.eyebrow": "LATEST INSIGHTS",
    "home.v3.insights.titleNormal": "Fresh perspectives for",
    "home.v3.insights.titleItalic": "better decisions.",
    "home.v3.insights.viewAll": "View all insights",
    "home.v3.insights.readInsight": "Read insight",
    "home.v3.insights.defaultCat": "TAXATION",
    "home.v3.insights.f1Date": "18 DECEMBER 2023",
    "home.v3.insights.f1Title":
      "New Business: Do You Need to Pay Tax Right Away?",
    "home.v3.insights.f1Excerpt":
      "Understanding initial tax registration obligations, NPWP requirements, and monthly reporting timelines for newly established entities in Indonesia.",
    "home.v3.insights.f2Date": "15 DECEMBER 2023",
    "home.v3.insights.f2Title":
      "Link Your NIK and NPWP Before the End of 2023!",
    "home.v3.insights.f2Excerpt":
      "Essential steps for corporate and individual taxpayers to validate NIK-NPWP integration and avoid administrative disruptions.",
    "home.v3.insights.f3Date": "18 OCTOBER 2023",
    "home.v3.insights.f3Title":
      "Annual Tax Return (SPT) Consultants in Tangerang",
    "home.v3.insights.f3Excerpt":
      "Practical guidance on preparing accurate fiscal reconciliations and annual tax filings with licensed tax consultants.",

    // Redesign Home - FAQ
    "home.v3.faq.eyebrow": "COMMON QUESTIONS",
    "home.v3.faq.titleNormal": "Things clients",
    "home.v3.faq.titleItalic": "often ask us.",
    "home.v3.faq.subtitle":
      "Clear answers on how we collaborate, structure our engagements, and support your tax and financial compliance.",
    "home.v3.faq.boxTitle": "Have a specific tax or compliance question?",
    "home.v3.faq.boxDesc":
      "Speak directly with our licensed consultants for advice tailored to your company's situation.",
    "home.v3.faq.boxCta": "Contact our team",
    "home.v3.faq.q1": "What types of businesses does AP Consulting work with?",
    "home.v3.faq.a1":
      "We serve a wide range of clients across Indonesia — from growing SMEs and startups to established national corporations and foreign-invested entities (PT PMA) requiring comprehensive tax, payroll, and accounting support.",
    "home.v3.faq.q2":
      "Can we engage AP Consulting for a single service, or do we need a full package?",
    "home.v3.faq.a2":
      "Our engagements are flexible. You can work with us for a specific requirement — such as a tax audit defense, SP2DK response, or monthly payroll administration — or retain us for integrated year-round tax and accounting compliance.",
    "home.v3.faq.q3":
      "How does AP Consulting handle tax disputes or audits with the tax authority?",
    "home.v3.faq.a3":
      "We represent and assist you throughout the entire process — from preparing explanations for SP2DK inquiries and managing tax audit data requests to filing formal objections and appeals with the Tax Court.",
    "home.v3.faq.q4": "How do we get started with AP Consulting?",
    "home.v3.faq.a4":
      "Simply reach out via our contact page, WhatsApp, or email to schedule an initial consultation. We will review your current situation, discuss your priorities, and provide a clear scope of work.",

    // Redesign Home - CTA
    "home.v3.cta.eyebrow": "LET’S TALK",
    "home.v3.cta.titlePrefix": "Ready to bring",
    "home.v3.cta.titleItalic": "clarity",
    "home.v3.cta.titleSuffix": "to your tax and accounting?",
    "home.v3.cta.description":
      "Speak with our team to find out how AP Consulting can support your business compliance and growth in Indonesia.",
    "home.v3.cta.primaryBtn": "Schedule a consultation",
    "home.v3.cta.whatsappBtn": "Chat on WhatsApp",

    // Home Hero
    "home.hero.title1": "CLEARER FINANCES.",
    "home.hero.titleHighlight": "STRONGER BUSINESS",
    "home.hero.title2": "DECISIONS.",
    "home.hero.description":
      "AP Consulting is a legal-based tax consulting firm with deep experience, professionalism, and high integrity in assisting clients (both corporate entities and individuals) to fulfill their tax obligations effectively.",
    "home.hero.exploreBtn": "EXPLORE OUR SERVICES",
    "home.hero.discussBtn": "DISCUSS YOUR NEEDS",

    // Home About AP Consulting
    "home.about.tag": "ABOUT AP CONSULTING",
    "home.about.titleNormal": "More than accountants.",
    "home.about.titleItalic": "Partners in your progress.",
    "home.about.description":
      "Our commitment is to be a reliable and trusted partner in resolving client challenges in accounting and taxation. Backed by an experienced team with deep tax insights, we deliver optimal service and concrete solutions for your business.",
    "home.about.button": "DISCOVER HOW WE HELP",

    // Home Stats
    "home.stats.tag": "AP CONSULTING AT A GLANCE",
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
    "home.whyChooseUs.card3Title": "One Stop Business Solution",
    "home.whyChooseUs.card3Body":
      "Comprehensive, integrated support covering tax, accounting, and business services to fulfill all your company's operational needs.",

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
      "Before starting a conversation, here are answers to the questions businesses commonly ask about AP Consulting's services, process, and engagement.",
    "home.faq.q1": "What services does AP Consulting provide?",
    "home.faq.a1":
      "AP Consulting provides professional consulting and advisory support across taxation, accounting and bookkeeping, and other customized financial services. The available scope depends on your business needs and will be discussed before an engagement begins.",
    "home.faq.q2": "How does AP Consulting work with us?",
    "home.faq.a2":
      "We start with an initial consultation to assess your business structure, records, and primary challenges. From there, we propose a customized scope of work tailored to your objectives and operational scale.",
    "home.faq.q3":
      "What information should I prepare before contacting AP Consulting?",
    "home.faq.a3":
      "Having basic information ready—such as your company structure, current accounting setup, recent financial reports, or tax documents—helps us understand your situation faster, though you are welcome to contact us even at an early stage.",
    "home.faq.q4":
      "Does AP Consulting support ongoing monthly services or only one-off projects?",
    "home.faq.a4":
      "We support both ongoing recurring retainer engagements (such as monthly bookkeeping or tax reporting) as well as project-based advisory services depending on your company's requirements.",
    "home.faq.q5": "How is engagement pricing set up?",
    "home.faq.a5":
      "Pricing is customized based on the scope, complexity, and transaction volume of your business operations. We provide a transparent scope and pricing proposal after our initial discussion.",

    // About Hero
    "about.hero.eyebrow": "ABOUT US · AP CONSULTING",
    "about.hero.title1": "Law-abiding tax experts.",
    "about.hero.title2": "Trusted partners in your growth.",
    "about.hero.description":
      "AP Consulting is a law-abiding taxation consultancy backed by a certified, experienced, and eclectic team in taxation and accountancy — helping corporate entities and individuals fulfill their tax obligations effectively.",
    "about.hero.discoverBtn": "Discover our profile",
    "about.hero.teamBtn": "Meet our leadership",
    "about.hero.banner": "INTEGRITY. EXCELLENCE. TEAMWORK.",

    // About AP Consulting Section
    "about.pcg.tag": "ABOUT AP CONSULTING",
    "about.pcg.titleNormal": "Certified tax expertise.",
    "about.pcg.titleItalic": "A partnership you can rely on.",
    "about.pcg.paragraph1":
      "AP Consulting is a law-abiding taxation consultancy that has a team consisting of certified, experienced & eclectic individuals in taxation and accountancy. We observe current, up-to-date laws and regulations in assisting both corporate entities and individual business owners.",
    "about.pcg.paragraph2":
      "Our commitment is to be a reliable and trustworthy business partner in resolving client challenges across taxation, payroll outsourcing, and accounting. Through solid teamwork, optimal client service, and concrete problem-solving, we help your business move forward with clarity.",

    // About Vision & Mission
    "about.vision.tag": "OUR VISION",
    "about.vision.title":
      "To be a trusted and law-abiding tax consulting partner that brings clarity, confidence, and sustainable growth to every business we serve.",
    "about.mission.tag": "OUR MISSION",
    "about.mission.title":
      "We help businesses navigate tax, payroll, and accounting complexity through certified expertise, strict confidentiality, and concrete problem-solving.",
    "about.mission.item1":
      "Deliver consistent, law-abiding tax & accounting services.",
    "about.mission.item2":
      "Protect client data under strict professional confidentiality.",
    "about.mission.item3":
      "Identify tax risks early and optimize compliant tax planning.",
    "about.mission.item4":
      "Represent clients reliably during DJP reviews, audits, and disputes.",
    "about.mission.item5":
      "Build long-term partnerships driven by integrity and excellence.",

    // About Why Choose AP Consulting
    "about.whyChoose.tag": "WHY CHOOSE AP CONSULTING",
    "about.whyChoose.titleNormal": "Support that",
    "about.whyChoose.titleItalic": "goes beyond the numbers.",
    "about.whyChoose.description":
      "AP Consulting helps businesses manage tax, payroll, and accounting obligations with a structured, law-abiding approach tailored to your industry.",
    "about.whyChoose.ctaButton": "Start a conversation",
    "about.whyChoose.card1Title": "Licensed & Law-Abiding Practice",
    "about.whyChoose.card1Body":
      "Registered tax consultants (IKPI) observing the latest Indonesian tax regulations with Practice License No. KIP-6592/IP.B/PJ/2020.",
    "about.whyChoose.card2Title": "Certified Tax Professionals",
    "about.whyChoose.card2Body":
      "Experienced and certified tax professionals dedicated to assisting corporate and individual clients with up-to-date tax laws.",
    "about.whyChoose.card3Title": "Strict Client Confidentiality",
    "about.whyChoose.card3Body":
      "Your financial and tax data stays strictly confidential. We never publish client names and protect every engagement.",
    "about.whyChoose.card4Title": "End-to-End Tax, Payroll & Accounting",
    "about.whyChoose.card4Body":
      "Integrated support covering monthly & annual SPT compliance, payroll PPh 21, fiscal reconciliation, and DJP audit representation.",

    // About People (Leadership Team)
    "about.people.tag": "OUR LEADERSHIP TEAM",
    "about.people.titleNormal": "Built by certified expertise.",
    "about.people.titleItalic": "Driven by integrity.",
    "about.people.description":
      "Meet the leadership and professionals behind AP Consulting — bringing hands-on tax experience, clear focus, and a shared commitment to your compliance.",
    "about.people.seeProfile": "See Profile",
    "about.people.licenseNote":
      "Practice License No. KIP-6592/IP.B/PJ/2020 · IKPI Member",

    // Services Hero
    "services.hero.eyebrow": "AP CONSULTING · OUR SERVICES",
    "services.hero.subtitleBanner":
      "HAVING TAX MANAGEMENT DIFFICULTIES? WE ARE HERE TO HELP.",
    "services.hero.titleBold": "Support designed around",
    "services.hero.titleItalic": "your business needs.",
    "services.hero.title": "Support designed around your business needs.",
    "services.hero.description":
      "We understand that taxation, payroll, and accounting administration can be tedious and time-consuming. AP Consulting provides consistent, legal-based professional services so you can focus on growing your market.",
    "services.hero.discoverBtn": "Explore our services",
    "services.hero.discussBtn": "Discuss your needs",

    // Services Scope of Support (AP Consulting 3 Core Services)
    "services.scope.tag": "SCOPE OF SUPPORT",
    "services.scope.titleItalic": "Practical support,",
    "services.scope.titleNormal": "from compliance to dispute resolution.",
    "services.scope.description":
      "Whether you are a newly established business or an expanding enterprise, AP Consulting provides structured tax, payroll outsourcing, and accounting services tailored to your operational needs.",
    "services.scope.includedLabel": "The services we provide include:",

    // 01. Tax Services
    "services.scope.s1Title": "Tax Services",
    "services.scope.s1Desc":
      "Tax administration can often be exhausting for businesses that are just starting out. That is why we are here to provide consulting and complete tax administration support.",
    "services.scope.s1Cta": "Discuss Tax Services",
    "services.scope.s1Sub1Title": "Tax Compliance Service",
    "services.scope.s1Sub1Desc":
      "Supporting taxpayers in fulfilling their tax obligations in accordance with prevailing laws and regulations — analyzing financial data, calculating, depositing taxes payable, preparing periodic and annual tax returns (SPT Masa & Tahunan), and reporting them on time.",
    "services.scope.s1Sub2Title": "Tax Review Service",
    "services.scope.s1Sub2Desc":
      "Offering tax review services to measure potential tax risks arising from corporate transactions and operations. This tax review report empowers clients to implement more effective tax planning.",
    "services.scope.s1Sub3Title": "Tax Audit & Examination Assistance",
    "services.scope.s1Sub3Desc":
      "Providing end-to-end representation throughout the tax examination process, including preparing income tax equalization data and documentary evidence, and providing relevant clarifications to tax authorities until the final decision is reached.",

    // 02. Payroll Outsourcing Services
    "services.scope.s2Title": "Payroll Outsourcing Services",
    "services.scope.s2Desc":
      "Meticulous, confidential, and timely payroll processing is what every client expects. We offer efficient payroll and income tax administration so clients can save valuable time while ensuring payroll records remain strictly confidential.",
    "services.scope.s2Cta": "Discuss Payroll Services",
    "services.scope.s2Sub1Title": "HR Administration & BPJS Registration",
    "services.scope.s2Sub1Desc":
      "Assisting with Human Resource-related administration such as registration for BPJS Kesehatan and BPJS Ketenagakerjaan, and more.",
    "services.scope.s2Sub2Title": "Payroll, Allowance & PPh 21 Calculation",
    "services.scope.s2Sub2Desc":
      "Accurate calculation of salary data, allowances, deductions, and Article 21 Income Tax (PPh 21) withholding for employees.",
    "services.scope.s2Sub3Title": "e-Payslip Generation",
    "services.scope.s2Sub3Desc":
      "Generating and securely distributing digital electronic pay slips (e-Payslips) directly to employees.",

    // 03. Accounting Services
    "services.scope.s3Title": "Accounting Services",
    "services.scope.s3Desc":
      "Sound bookkeeping is the key to the successful growth of any business. By entrusting their accounting to our professional team, companies can stay fully focused on expanding their business.",
    "services.scope.s3Cta": "Discuss Accounting Services",
    "services.scope.s3Sub1Title": "Financial Statement Preparation",
    "services.scope.s3Sub1Desc":
      "Preparation of comprehensive financial statements for corporations (PT), limited partnerships (CV), sole proprietorships, MSMEs (UMKM), and others.",
    "services.scope.s3Sub2Title": "Accounting Review Services",
    "services.scope.s3Sub2Desc":
      "Identifying and verifying source documents as well as supervising transaction recording and the integrity of financial statements.",
    "services.scope.s3Sub3Title": "Internal Audit",
    "services.scope.s3Sub3Desc":
      "Conducting systematic internal audits to evaluate internal accounting controls, financial accuracy, and operational compliance.",
    "services.scope.s3Sub4Title": "Accounting Software Consultation",
    "services.scope.s3Sub4Desc":
      "Providing expert consultation and setup assistance for accounting software tailored to the company's operational requirements.",

    // Services Current Need
    "services.need.tag": "START WITH YOUR CURRENT NEED",
    "services.need.titleNormal": "Where does your business",
    "services.need.titleItalic": "need more clarity?",
    "services.need.subtitle":
      "Choose the priority below that matches your current situation. We tailor our scope of engagement around what your business needs right now.",
    "services.need.n1Title": "Tax Compliance & Reporting",
    "services.need.n1Body":
      "Fulfill monthly and annual SPT obligations accurately and on time without administrative stress.",
    "services.need.n2Title": "Payroll & PPh 21 Processing",
    "services.need.n2Body":
      "Ensure timely salaries, BPJS compliance, and confidential e-Payslips for your employees.",
    "services.need.n3Title": "Bookkeeping & Financial Reports",
    "services.need.n3Body":
      "Prepare accurate financial statements for PT, CV, or UMKM and optimize your accounting software.",
    "services.need.n4Title": "Tax Review & Audit Assistance",
    "services.need.n4Body":
      "Identify tax exposure through reviews and secure professional assistance during tax office examinations.",

    // Services Overrides
    "services.ourApproach.tag": "HOW WE WORK",
    "services.ourApproach.titlePrefix": "From ",
    "services.ourApproach.titleHighlight": "understanding ",
    "services.ourApproach.titleSuffix": "to confident action.",
    "services.ourApproach.subtitle":
      "Managing and growing your business with strategic tax and financial advice starts with four clear steps.",
    "services.cta.tag": "START A CONVERSATION",
    "services.cta.titleNormal": "Not sure which service is right for you?",
    "services.cta.description":
      "Tell us about your current situation. We'll help you define the right scope of support for your business.",
    "services.cta.buttonText": "Discuss Your Needs",

    // Insights Hero
    "insight.hero.eyebrow": "AP CONSULTING · INSIGHTS & ARTICLES",
    "insight.hero.tag": "FEATURED ARTICLE",
    "insight.hero.title": "New Business: Do You Need to Pay Tax Right Away?",
    "insight.hero.description":
      "Taxation is an essential part of national economic sustainability. Understanding tax obligations from day one helps newly established businesses stay compliant and avoid unnecessary penalties.",
    "insight.hero.author": "By AP Consulting Editorial Team",
    "insight.hero.date": "18 December 2023",
    "insight.hero.readTime": "5 min read",
    "insight.hero.readFeatured": "Read full article",

    // Insights Section
    "insight.articles.eyebrow": "LATEST ARTICLES",
    "insight.articles.titleBold": "Practical insights",
    "insight.articles.titleItalic": "for smarter tax decisions.",
    "insight.articles.title": "Latest Articles & Insights",
    "insight.articles.subtitle":
      "Stay informed with the latest Indonesian tax regulations, compliance guides, and practical accounting perspectives.",
    "insight.articles.readBtn": "Read more",
    "insight.articles.sidebarTitle": "Articles Categories",
    "insight.articles.showing": "Showing page",
    "insight.articles.of": "of",
    "insight.articles.articlesLabel": "Articles",

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
    "insight.detail.share": "Share",
    "insight.detail.linkCopied": "Link copied to clipboard",
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
    "contact.hero.tag": "CONTACT US · AP CONSULTING",
    "contact.hero.titleLight": "Get in touch ",
    "contact.hero.titleItalic": "with AP Consulting.",
    "contact.hero.headerSubtitle":
      "Any questions? Please do not hesitate to reach out.",
    "contact.hero.headerDesc":
      "Send us a message via the contact form below or connect directly with our team on WhatsApp.",
    "contact.hero.cardTitle": "Reach Us Directly",
    "contact.hero.cardDesc":
      "Or you can simply reach our WhatsApp number on +62 857 5417 2217.",
    "contact.hero.whatsappCta": "Chat on WhatsApp",
    "contact.hero.emailLabel": "Email",
    "contact.hero.phoneLabel": "Phone / WhatsApp",
    "contact.form.title": "Send us a message",
    "contact.form.subtitle":
      "Share a little about your business, your current challenge, or the service you would like to discuss.",
    "contact.form.fullName": "Full Name",
    "contact.form.fullNamePlaceholder": "Enter your name",
    "contact.form.email": "Email Address",
    "contact.form.emailPlaceholder": "you@example.com",
    "contact.form.service": "Service of interest",
    "contact.form.selectOne": "Select one",
    "contact.form.optTax": "Tax Services",
    "contact.form.optAcc": "Payroll Outsourcing Services",
    "contact.form.optBiz": "Tax Accounting Services",
    "contact.form.optFin": "Tax Audit & Dispute Resolution",
    "contact.form.optGen": "General Inquiry",
    "contact.form.phone": "Phone number (optional)",
    "contact.form.company": "Company Name (optional)",
    "contact.form.companyPlaceholder": "e.g. PT Maju Bersama",
    "contact.form.message": "Message",
    "contact.form.messagePlaceholder": "Tell us how we can help your business",
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
    "contact.location.tag": "OUR OFFICES",
    "contact.location.titleNormal": "Visit our",
    "contact.location.titleItalic": "Jakarta & Tangerang offices.",
    "contact.location.title": "Visit Our Offices",
    "contact.location.address":
      "SUN Plaza, Jl. Meruya Ilir Raya No. 20, Jakarta Barat",
    "contact.location.openMaps": "Open in Google Maps",
    "contact.location.loadMap": "Load Interactive Map",

    // Contact CTA Overrides
    "contact.cta.tag": "START A CONVERSATION",
    "contact.cta.titleItalic": "Ready to take the next step?",
    "contact.cta.description":
      "Discuss your business needs with AP Consulting.",
    "contact.cta.buttonText": "Discuss Your Needs",
  },
  id: {
    "nav.home": "Beranda",
    "nav.services": "Layanan",
    "nav.insights": "Wawasan",
    "nav.about": "Tentang Kami",
    "nav.contact": "Kontak",
    "nav.consultation": "Konsultasi",
    "nav.discussNeeds": "Diskusikan Kebutuhanmu",
    "cta.tag": "MULAI DISKUSI",
    "cta.titleNormal": "Siap membawa kejelasan untuk bisnis Anda?",
    "cta.titleItalic": "Mari ambil langkah selanjutnya",
    "cta.description":
      "Ceritakan kebutuhan pajak, akuntansi, maupun keuangan Anda kepada kami. AP Consulting siap membantu Anda menemukan langkah awal yang paling tepat untuk bisnis Anda.",
    "cta.buttonText": "Diskusikan Kebutuhanmu",
    "footer.tagline":
      "Dukungan profesional untuk urusan pajak, akuntansi, dan layanan keuangan. Kami hadir untuk membantu bisnismu melangkah maju dengan penuh keyakinan.",
    "footer.rights": "Hak Cipta Dilindungi",
    "footer.quickLinks": "Tautan Cepat",
    "footer.services": "Layanan Kami",
    "footer.contactUs": "Hubungi Kami",
    "footer.headOffice": "Kantor Pusat",
    "footer.branchOffice": "Kantor Cabang",
    "footer.licensedConsultants": "Konsultan pajak berlisensi resmi",
    "footer.ikpiMember": "Anggota Ikatan Konsultan Pajak Indonesia (IKPI)",
    "footer.practiceLicensePrefix": "Izin Praktik No.",
    "footer.viewLicenses": "Lihat lisensi",
    "footer.copiedLicense": "Nama disalin! Membuka SIKOP...",
    "footer.language": "Bahasa:",

    // Redesign Home - Hero
    "home.v3.hero.eyebrow": "AP CONSULTING · LAYANAN PAJAK & KONSULTASI",
    "home.v3.hero.titleBold": "Pajak lebih jelas.",
    "home.v3.hero.titleMiddle": "Bawa bisnis Anda melangkah",
    "home.v3.hero.titleItalic": "maju dengan yakin.",
    "home.v3.hero.subtitle":
      "Dukungan perpajakan dan akuntansi profesional, agar Anda dapat fokus mengembangkan bisnis Anda.",
    "home.v3.hero.cta": "Diskusikan kebutuhan Anda",
    "home.v3.hero.banner": "INTEGRITAS. KEUNGGULAN. KERJA SAMA TIM.",
    "home.v3.hero.stat1Label": "Tahun pengalaman di bidang pajak & akuntansi",
    "home.v3.hero.stat2Label": "Klien badan usaha & perorangan aktif",
    "home.v3.hero.stat3Label": "Pelaporan SPT yang ditangani setiap tahun",
    "home.v3.hero.stat4Label": "Konsultan pajak bersertifikat",

    // Redesign Home - Services (Our Expertise)
    "home.v3.services.eyebrow": "KEAHLIAN KAMI",
    "home.v3.services.titleBold": "Dukungan yang tepat",
    "home.v3.services.titleItalic": "untuk bisnis Anda.",
    "home.v3.services.explore": "Jelajahi layanan kami",
    "home.v3.services.learnMore": "Pelajari selengkapnya",
    "home.v3.services.s1Title": "Jasa Perpajakan",
    "home.v3.services.s1Desc":
      "Konsultasi dan pemenuhan administrasi perpajakan: Jasa Tax Compliance, Penelaahan Pajak, dan Pendampingan Pemeriksaan Pajak.",
    "home.v3.services.s2Title": "Jasa Payroll Outsourcing",
    "home.v3.services.s2Desc":
      "Pengelolaan data dan penghitungan gaji yang teliti, rahasia, dan tepat waktu, termasuk administrasi BPJS dan e-Payslip.",
    "home.v3.services.s3Title": "Jasa Akuntansi",
    "home.v3.services.s3Desc":
      "Pembukuan usaha, penyusunan laporan keuangan (PT/CV/UMKM), penelaahan akuntansi, audit internal, hingga konsultasi software akuntansi.",

    // Redesign Home - Sectors (Industries We Serve)
    "home.v3.sectors.eyebrow": "INDUSTRI YANG KAMI LAYANI",
    "home.v3.sectors.titleNormal": "Berpengalaman di berbagai",
    "home.v3.sectors.titleItalic": "sektor industri Anda.",
    "home.v3.sectors.description":
      "Kami mendampingi bisnis dari berbagai skala, mulai dari UMKM lokal hingga perusahaan penanaman modal asing, di berbagai sektor berikut.",
    "home.v3.sectors.confidentialBold":
      "Data Anda tetap terjaga kerahasiaannya.",
    "home.v3.sectors.confidentialBody":
      "Kami tidak pernah mempublikasikan nama klien. Setiap kerja sama ditangani dengan standar kerahasiaan yang ketat.",
    "home.v3.sectors.item1": "Manufaktur",
    "home.v3.sectors.item2": "Perdagangan & Distribusi",
    "home.v3.sectors.item3": "Properti & Konstruksi",
    "home.v3.sectors.item4": "Makanan & Minuman",
    "home.v3.sectors.item5": "Teknologi & Startup",
    "home.v3.sectors.item6": "Penanaman Modal Asing (PT PMA)",

    // Redesign Home - About
    "home.v3.about.eyebrow": "TENTANG AP CONSULTING",
    "home.v3.about.titleBold": "Keahlian perpajakan.",
    "home.v3.about.titleMiddle": "Kemitraan yang",
    "home.v3.about.titleItalic": "dapat Anda andalkan.",
    "home.v3.about.p1":
      "AP Consulting merupakan kantor konsultan pajak berbasis hukum yang berpengalaman, berintegritas tinggi, dan profesional dalam membantu klien — baik badan usaha maupun perorangan — memenuhi kewajiban perpajakannya secara efektif sesuai peraturan terbaru.",
    "home.v3.about.p2":
      "Komitmen kami adalah menjadi mitra perusahaan yang handal dan terpercaya dalam menyelesaikan permasalahan klien di bidang perpajakan dan akuntansi. Melalui kerja sama tim yang solid, pelayanan optimal, serta penyelesaian masalah yang konkrit, kami membantu bisnis Anda melangkah maju dengan jelas.",
    "home.v3.about.cta": "Kenali AP Consulting lebih dekat",

    // Redesign Home - Values
    "home.v3.values.eyebrow": "NILAI-NILAI YANG KAMI JUNJUNG",
    "home.v3.values.titleBold": "Lebih dari sekadar angka.",
    "home.v3.values.titleMiddle": "Ini tentang",
    "home.v3.values.titleItalic": "kepercayaan.",
    "home.v3.values.quote":
      "“Jika Anda gagal merencanakan, Anda merencanakan kegagalan — Rencanakan pajak Anda bersama kami.”",
    "home.v3.values.v1Title": "Integritas",
    "home.v3.values.v1Desc":
      "Kami selalu disiplin dan konsisten dalam memberikan layanan terbaik demi mencapai tujuan perusahaan klien.",
    "home.v3.values.v2Title": "Keunggulan",
    "home.v3.values.v2Desc":
      "Kami senantiasa berkomitmen menghadirkan kualitas layanan prima secara konsisten melalui tim profesional kami.",
    "home.v3.values.v3Title": "Kerja Sama Tim",
    "home.v3.values.v3Desc":
      "Kami meyakini kerja sama tim yang solid sangat penting untuk meningkatkan motivasi dan keahlian dalam menemukan solusi terbaik.",

    // Redesign Home - Approach
    "home.v3.approach.eyebrow": "PENDEKATAN KAMI",
    "home.v3.approach.titleBold": "Cara kami bekerja",
    "home.v3.approach.titleItalic": "bersama bisnis Anda.",
    "home.v3.approach.subtitle":
      "Kerangka kerja pendampingan empat tahap yang terstruktur untuk memastikan kepatuhan pajak, penggajian, dan laporan keuangan perusahaan Anda selalu terjaga.",
    "home.v3.approach.s1Title": "Memahami Kondisi Usaha",
    "home.v3.approach.s1Desc":
      "Kami mempelajari bentuk usaha, alur transaksi, dan status kepatuhan Anda secara mendalam sebelum menentukan langkah penanganan.",
    "home.v3.approach.s2Title": "Solusi yang Disesuaikan",
    "home.v3.approach.s2Desc":
      "Setiap bisnis memiliki kebutuhan berbeda. Kami merancang rencana praktis sesuai ruang lingkup pajak, payroll, atau akuntansi Anda tanpa kerumitan yang tidak perlu.",
    "home.v3.approach.s3Title": "Komunikasi Terbuka & Jelas",
    "home.v3.approach.s3Desc":
      "Kami menyampaikan setiap regulasi, perhitungan, dan laporan dalam bahasa yang lugas dan transparan agar Anda dapat mengambil keputusan dengan mantap.",
    "home.v3.approach.s4Title": "Pendampingan Berkelanjutan",
    "home.v3.approach.s4Desc":
      "Urusan pajak dan keuangan merupakan komitmen berkelanjutan. Kami mendampingi Anda sepanjang tahun untuk memenuhi tenggat waktu dan mendukung pertumbuhan bisnis.",

    // Redesign Home - Insights
    "home.v3.insights.eyebrow": "WAWASAN TERBARU",
    "home.v3.insights.titleNormal": "Perspektif segar untuk",
    "home.v3.insights.titleItalic": "keputusan yang lebih tepat.",
    "home.v3.insights.viewAll": "Lihat semua wawasan",
    "home.v3.insights.readInsight": "Baca artikel",
    "home.v3.insights.defaultCat": "PERPAJAKAN",
    "home.v3.insights.f1Date": "18 DESEMBER 2023",
    "home.v3.insights.f1Title":
      "Baru Buat Usaha, Apakah Harus Langsung Bayar Pajak?",
    "home.v3.insights.f1Excerpt":
      "Memahami kewajiban pendaftaran pajak awal, persyaratan NPWP, dan jadwal pelaporan bulanan bagi badan usaha yang baru didirikan di Indonesia.",
    "home.v3.insights.f2Date": "15 DESEMBER 2023",
    "home.v3.insights.f2Title":
      "Segera Padankan NIK dan NPWP Sebelum Akhir Tahun 2023!",
    "home.v3.insights.f2Excerpt":
      "Langkah-langkah penting bagi wajib pajak badan maupun orang pribadi untuk memvalidasi pemadanan NIK-NPWP agar terhindar dari kendala administrasi perpajakan.",
    "home.v3.insights.f3Date": "18 OKTOBER 2023",
    "home.v3.insights.f3Title": "Konsultan Pajak SPT Tahunan di Tangerang",
    "home.v3.insights.f3Excerpt":
      "Panduan praktis dalam menyiapkan rekonsiliasi fiskal yang akurat dan pelaporan SPT Tahunan bersama konsultan pajak berlisensi resmi.",

    // Redesign Home - FAQ
    "home.v3.faq.eyebrow": "PERTANYAAN UMUM",
    "home.v3.faq.titleNormal": "Hal-hal yang sering",
    "home.v3.faq.titleItalic": "ditanyakan klien.",
    "home.v3.faq.subtitle":
      "Jawaban jelas mengenai cara kami berkolaborasi, menyusun ruang lingkup kerja sama, serta mendukung kepatuhan pajak dan keuangan Anda.",
    "home.v3.faq.boxTitle":
      "Punya pertanyaan khusus terkait pajak atau kepatuhan?",
    "home.v3.faq.boxDesc":
      "Diskusikan langsung bersama konsultan berlisensi kami untuk mendapatkan saran yang sesuai dengan situasi perusahaan Anda.",
    "home.v3.faq.boxCta": "Hubungi tim kami",
    "home.v3.faq.q1":
      "Jenis bisnis apa saja yang didampingi oleh AP Consulting?",
    "home.v3.faq.a1":
      "Kami melayani beragam klien di seluruh Indonesia — mulai dari UMKM yang sedang berkembang dan startup hingga perusahaan nasional serta perusahaan penanaman modal asing (PT PMA) yang membutuhkan dukungan perpajakan, payroll, dan akuntansi yang komprehensif.",
    "home.v3.faq.q2":
      "Apakah kami bisa menggunakan jasa AP Consulting untuk satu layanan saja, atau harus paket lengkap?",
    "home.v3.faq.a2":
      "Kerja sama kami bersifat fleksibel. Anda dapat bekerja sama dengan kami untuk kebutuhan spesifik — seperti pendampingan pemeriksaan pajak, tanggapan SP2DK, atau administrasi payroll bulanan — maupun pendampingan terpadu sepanjang tahun.",
    "home.v3.faq.q3":
      "Bagaimana AP Consulting menangani sengketa atau pemeriksaan pajak dengan otoritas pajak?",
    "home.v3.faq.a3":
      "Kami mewakili dan mendampingi Anda di sepanjang proses — mulai dari menyiapkan penjelasan atas permintaan keterangan SP2DK, mengelola permintaan data pemeriksaan pajak, hingga pengajuan keberatan dan banding di Pengadilan Pajak.",
    "home.v3.faq.q4": "Bagaimana cara memulai kerja sama dengan AP Consulting?",
    "home.v3.faq.a4":
      "Cukup hubungi kami melalui halaman kontak, WhatsApp, atau email untuk menjadwalkan konsultasi awal. Kami akan meninjau kondisi Anda saat ini, mendiskusikan prioritas Anda, dan menyusun ruang lingkup pekerjaan yang jelas.",

    // Redesign Home - CTA
    "home.v3.cta.eyebrow": "MARI BERDISKUSI",
    "home.v3.cta.titlePrefix": "Siap menghadirkan",
    "home.v3.cta.titleItalic": "kejelasan",
    "home.v3.cta.titleSuffix": "pada urusan pajak dan akuntansi Anda?",
    "home.v3.cta.description":
      "Diskusikan dengan tim kami untuk mengetahui bagaimana AP Consulting dapat mendukung kepatuhan dan pertumbuhan bisnis Anda di Indonesia.",
    "home.v3.cta.primaryBtn": "Jadwalkan konsultasi",
    "home.v3.cta.whatsappBtn": "Chat via WhatsApp",

    // Home Hero
    "home.hero.title1": "PAHAMI KEUANGAN ANDA,",
    "home.hero.titleHighlight": "AMBIL KEPUTUSAN BISNIS",
    "home.hero.title2": "DENGAN LEBIH YAKIN.",
    "home.hero.description":
      "AP Consulting merupakan kantor konsultan pajak berbasis hukum yang berpengalaman, profesional dan berintegritas dalam membantu klien (badan usaha ataupun perorangan) dalam memenuhi kewajiban perpajakannya secara efektif.",
    "home.hero.exploreBtn": "PELAJARI LAYANAN KAMI",
    "home.hero.discussBtn": "DISKUSIKAN KEBUTUHANMU",

    // Home About AP Consulting
    "home.about.tag": "TENTANG AP CONSULTING",
    "home.about.titleNormal": "Lebih dari sekadar akuntan.",
    "home.about.titleItalic": "Kami Mitra Kesuksesan Anda.",
    "home.about.description":
      "Komitmen kami adalah menjadi mitra perusahaan yang handal dan terpercaya dalam menyelesaikan permasalahan klien di bidang akuntansi dan perpajakan. Didukung tim berpengalaman dan berwawasan luas, kami menghadirkan pelayanan optimal serta solusi konkrit bagi bisnis Anda.",
    "home.about.button": "CARI TAHU LEBIH LANJUT",

    // Home Stats
    "home.stats.tag": "SEKILAS TENTANG AP CONSULTING",
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
    "home.whyChooseUs.card3Title": "One Stop Business Solution",
    "home.whyChooseUs.card3Body":
      "Dukungan terpadu mulai dari perpajakan, akuntansi, hingga administrasi bisnis untuk memenuhi segala kebutuhan usaha Anda.",

    // Home Our Approach
    "home.ourApproach.tag": "PENDEKATAN KAMI",
    "home.ourApproach.titlePrefix": "Dari ",
    "home.ourApproach.titleHighlight": "Kejelasan Keuangan",
    "home.ourApproach.titleSuffix": "Menuju Langkah Pasti.",
    "home.ourApproach.subtitle":
      "Pendekatan terstruktur untuk membantu bisnis Anda tetap patuh, terorganisir, dan siap menghadapi masa depan.",
    "home.ourApproach.step1Title": "Memahami Bisnis Anda Sepenuhnya",
    "home.ourApproach.step1Body":
      "Kami mengevaluasi posisi keuangan, catatan, dan proses administratif Anda untuk memahami kondisi bisnis Anda saat ini.",
    "home.ourApproach.step2Title": "Mengidentifikasi Prioritas",
    "home.ourApproach.step2Body":
      "Kami mengidentifikasi risiko, celah, dan prioritas, lalu berfokus pada area yang membutuhkan perhatian terlebih dahulu.",
    "home.ourApproach.step3Title": "Menerapkan Solusi Yang Tepat",
    "home.ourApproach.step3Body":
      "Kami menerapkan solusi perpajakan, akuntansi, dan keuangan yang disepakati sesuai dengan kebutuhan bisnis Anda.",
    "home.ourApproach.step4Title": "Evaluasi dan Terus Melangkah Maju",
    "home.ourApproach.step4Body":
      "Kami mengevaluasi perkembangannya secara berkala dan siap memberikan dukungan berkelanjutan seiring dengan bertumbuhnya bisnis Anda.",

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
      "Sebelum memulai percakapan, berikut adalah jawaban atas pertanyaan yang sering diajukan bisnis mengenai layanan, proses, dan kerja sama dengan AP Consulting.",
    "home.faq.q1": "Layanan apa saja yang disediakan oleh AP Consulting?",
    "home.faq.a1":
      "AP Consulting menyediakan dukungan konsultasi profesional di bidang perpajakan, akuntansi & pembukuan, serta layanan keuangan kustom lainnya. Cakupan layanan disesuaikan dengan kebutuhan bisnis Anda dan akan didiskusikan sebelum kerja sama dimulai.",
    "home.faq.q2": "Bagaimana cara kerja sama dengan AP Consulting?",
    "home.faq.a2":
      "Kami memulai dengan konsultasi awal untuk mengevaluasi struktur bisnis, catatan, dan tantangan utama Anda. Dari sana, kami mengusulkan cakupan kerja kustom yang disesuaikan dengan tujuan dan skala operasional Anda.",
    "home.faq.q3":
      "Informasi apa yang perlu disiapkan sebelum menghubungi AP Consulting?",
    "home.faq.a3":
      "Menyiapkan informasi dasar—seperti struktur perusahaan, sistem akuntansi saat ini, laporan keuangan terbaru, atau dokumen pajak—membantu kami memahami situasi Anda lebih cepat, meskipun Anda tetap bisa menghubungi kami sejak tahap awal.",
    "home.faq.q4":
      "Apakah AP Consulting mendukung layanan bulanan berkelanjutan atau hanya proyek sekali jalan?",
    "home.faq.a4":
      "Kami mendukung kontrak bulanan berkelanjutan (seperti pembukuan bulanan atau pelaporan pajak) maupun layanan konsultasi berbasis proyek sesuai dengan kebutuhan perusahaan Anda.",
    "home.faq.q5": "Bagaimana penentuan harga layanan AP Consulting?",
    "home.faq.a5":
      "Harga disesuaikan berdasarkan cakupan, kompleksitas, dan volume transaksi operasional bisnis Anda. Kami memberikan proposal cakupan dan harga yang transparan setelah diskusi awal.",

    // About Hero
    "about.hero.eyebrow": "TENTANG KAMI · AP CONSULTING",
    "about.hero.title1": "Konsultan pajak taat hukum.",
    "about.hero.title2": "Mitra tepercaya pertumbuhan Anda.",
    "about.hero.description":
      "AP Consulting adalah kantor konsultan pajak berbasis hukum yang didukung tim bersertifikat, berpengalaman, dan profesional di bidang perpajakan serta akuntansi — membantu badan usaha maupun perorangan memenuhi kewajiban pajak secara efektif.",
    "about.hero.discoverBtn": "Pelajari profil kami",
    "about.hero.teamBtn": "Kenali tim pimpinan",
    "about.hero.banner": "INTEGRITAS. KEUNGGULAN. KERJA SAMA TIM.",

    // About AP Consulting Section
    "about.pcg.tag": "TENTANG AP CONSULTING",
    "about.pcg.titleNormal": "Keahlian pajak bersertifikat.",
    "about.pcg.titleItalic": "Kemitraan yang dapat diandalkan.",
    "about.pcg.paragraph1":
      "AP Consulting merupakan kantor konsultan pajak berbasis hukum yang memiliki tim profesional bersertifikat, berpengalaman, dan berwawasan luas di bidang perpajakan serta akuntansi. Kami senantiasa mengikuti peraturan perundang-undangan terbaru dalam mendampingi klien badan usaha maupun wajib pajak orang pribadi.",
    "about.pcg.paragraph2":
      "Komitmen kami adalah menjadi mitra bisnis yang handal dan tepercaya dalam menyelesaikan tantangan klien di bidang perpajakan, payroll outsourcing, dan akuntansi. Melalui kerja sama tim yang solid, pelayanan optimal, serta penyelesaian masalah yang konkrit, kami membantu bisnis Anda melangkah maju dengan jelas.",

    // About Vision & Mission
    "about.vision.tag": "VISI KAMI",
    "about.vision.title":
      "Menjadi mitra konsultan pajak yang tepercaya dan taat hukum, menghadirkan kejelasan, rasa yakin, serta pertumbuhan berkelanjutan bagi setiap bisnis yang kami layani.",
    "about.mission.tag": "MISI KAMI",
    "about.mission.title":
      "Kami membantu pelaku usaha mengelola kerumitan pajak, payroll, dan akuntansi melalui keahlian bersertifikat, kerahasiaan data yang ketat, dan solusi konkrit.",
    "about.mission.item1":
      "Menghadirkan layanan pajak & akuntansi yang konsisten dan taat hukum.",
    "about.mission.item2":
      "Menjaga kerahasiaan data klien dengan standar profesional yang ketat.",
    "about.mission.item3":
      "Mengidentifikasi risiko pajak sejak dini dan menyusun perencanaan pajak patuh aturan.",
    "about.mission.item4":
      "Mendampingi klien secara profesional dalam peninjauan, pemeriksaan DJP, dan sengketa pajak.",
    "about.mission.item5":
      "Membangun kemitraan jangka panjang berlandaskan integritas dan keunggulan.",

    // About Why Choose AP Consulting
    "about.whyChoose.tag": "MENGAPA MEMILIH AP CONSULTING",
    "about.whyChoose.titleNormal": "Dukungan yang",
    "about.whyChoose.titleItalic": "melampaui sekadar angka.",
    "about.whyChoose.description":
      "AP Consulting membantu perusahaan mengelola kewajiban perpajakan, payroll, dan akuntansi melalui pendekatan terstruktur dan taat hukum sesuai karakter industri Anda.",
    "about.whyChoose.ctaButton": "Mulai percakapan",
    "about.whyChoose.card1Title": "Praktik Berlisensi & Taat Hukum",
    "about.whyChoose.card1Body":
      "Konsultan pajak terdaftar (IKPI) yang mematuhi regulasi perpajakan Indonesia terbaru dengan Izin Praktik No. KIP-6592/IP.B/PJ/2020.",
    "about.whyChoose.card2Title": "Tim Profesional Bersertifikat",
    "about.whyChoose.card2Body":
      "Didukung konsultan pajak berpengalaman dan bersertifikat resmi dalam mendampingi kepatuhan klien korporasi maupun perorangan.",
    "about.whyChoose.card3Title": "Kerahasiaan Data Terjamin",
    "about.whyChoose.card3Body":
      "Data keuangan dan pajak Anda dijaga kerahasiaannya secara ketat. Kami tidak pernah mempublikasikan nama klien.",
    "about.whyChoose.card4Title": "Layanan Pajak, Payroll & Akuntansi Terpadu",
    "about.whyChoose.card4Body":
      "Dukungan menyeluruh mulai dari pelaporan SPT Masa & Tahunan, payroll PPh 21, rekonsiliasi fiskal, hingga pendampingan pemeriksaan DJP.",

    // About People (Leadership Team)
    "about.people.tag": "TIM PIMPINAN KAMI",
    "about.people.titleNormal": "Dibangun oleh keahlian bersertifikat.",
    "about.people.titleItalic": "Digerakkan oleh integritas.",
    "about.people.description":
      "Kenali pimpinan dan tim profesional di balik AP Consulting — menghadirkan pengalaman praktis perpajakan, fokus yang jelas, dan komitmen penuh untuk kepatuhan bisnis Anda.",
    "about.people.seeProfile": "Lihat Profil",
    "about.people.licenseNote":
      "Izin Praktik No. KIP-6592/IP.B/PJ/2020 · Anggota IKPI",

    // Services Hero
    "services.hero.eyebrow": "AP CONSULTING · LAYANAN KAMI",
    "services.hero.subtitleBanner":
      "MENGALAMI KESULITAN MANAJEMEN PAJAK? KAMI SIAP MEMBANTU.",
    "services.hero.titleBold": "Dukungan yang dirancang",
    "services.hero.titleItalic": "untuk kebutuhan bisnis Anda.",
    "services.hero.title":
      "Dukungan yang dirancang khusus untuk kebutuhan bisnis Anda.",
    "services.hero.description":
      "Kami memahami bahwa administrasi perpajakan, penggajian, dan akuntansi sering kali menyita banyak waktu dan tenaga. AP Consulting menghadirkan layanan profesional berbasis hukum yang konsisten agar Anda dapat fokus mengembangkan pasar.",
    "services.hero.discoverBtn": "Jelajahi layanan kami",
    "services.hero.discussBtn": "Diskusikan kebutuhan Anda",

    // Services Scope of Support (AP Consulting 3 Core Services)
    "services.scope.tag": "RUANG LINGKUP LAYANAN",
    "services.scope.titleItalic": "Dukungan praktis,",
    "services.scope.titleNormal":
      "mulai dari kepatuhan hingga pendampingan pemeriksaan.",
    "services.scope.description":
      "Baik untuk perusahaan yang baru merintis maupun bisnis yang sedang berkembang pesat, AP Consulting menyediakan jasa perpajakan, payroll outsourcing, dan akuntansi terstruktur yang disesuaikan dengan kebutuhan Anda.",
    "services.scope.includedLabel": "Jasa yang kami berikan meliputi:",

    // 01. Tax Services
    "services.scope.s1Title": "Jasa Perpajakan",
    "services.scope.s1Desc":
      "Administrasi perpajakan terkadang sangat melelahkan bagi sebuah perusahaan yang baru saja merintis usahanya, oleh karena itu kami hadir untuk membantu memberikan konsultasi dan pemenuhan administrasi perpajakan.",
    "services.scope.s1Cta": "Diskusikan Layanan Pajak",
    "services.scope.s1Sub1Title": "Jasa Tax Compliance",
    "services.scope.s1Sub1Desc":
      "Kami memberikan jasa dalam mendukung wajib pajak untuk memenuhi kewajiban perpajakan sesuai ketentuan perundang-undangan yang berlaku dengan cara menganalisa data, menghitung, menyetorkan pajak terutang, mempersiapkan SPT Masa dan Tahunan serta melaporkannya dengan tepat waktu.",
    "services.scope.s1Sub2Title": "Jasa Penelaahan Pajak",
    "services.scope.s1Sub2Desc":
      "Kami menawarkan jasa penelaahan pajak untuk mengukur potensi risiko wajib pajak dari aktivitas dan transaksi perusahaan. Dari Laporan hasil penelaahan pajak ini akan memudahkan klien untuk perencanaan pajak yang lebih efektif.",
    "services.scope.s1Sub3Title":
      "Jasa Pendampingan Klien dalam Pemeriksaan Pajak",
    "services.scope.s1Sub3Desc":
      "Kami memberikan pendampingan dalam proses pemeriksaan sampai selesai, termasuk dalam hal menyiapkan data ekualisasi pajak penghasilan dan pembuktian dokumen, memberikan keterangan kepada fiskus sampai mendapatkan hasil keputusan.",

    // 02. Payroll Outsourcing Services
    "services.scope.s2Title": "Jasa Payroll Outsourcing",
    "services.scope.s2Desc":
      "Penghitungan Payroll yang teliti, rahasia dan tepat waktu merupakan keinginan dari klien. Kami menawarkan pengelolaan data dan penghitungan gaji beserta pajak penghasilannya yang akan dilakukan secara efektif sehingga klien bisa lebih menghemat waktu mereka dan data gaji akan senantiasa rahasia.",
    "services.scope.s2Cta": "Diskusikan Layanan Payroll",
    "services.scope.s2Sub1Title": "Administrasi HR & Pendaftaran BPJS",
    "services.scope.s2Sub1Desc":
      "Membantu melakukan administrasi yang berhubungan dengan Human Resource seperti Pendaftaran BPJS Kesehatan dan Tenagakerja, dll.",
    "services.scope.s2Sub2Title": "Penghitungan Gaji, Tunjangan & Pajak PPh 21",
    "services.scope.s2Sub2Desc":
      "Penghitungan data gaji, tunjangan, dan potongan serta Pajak PPh 21 untuk karyawan.",
    "services.scope.s2Sub3Title": "Pembuatan e-Payslip",
    "services.scope.s2Sub3Desc":
      "Pembuatan dan penerbitan slip gaji digital (e-Payslip) secara terstruktur, rahasia, dan tepat waktu.",

    // 03. Accounting Services
    "services.scope.s3Title": "Jasa Akuntansi",
    "services.scope.s3Desc":
      "Pembukuan yang baik merupakan kunci kesuksesan pengembangan suatu usaha. Dengan memberikan akuntansi-nya untuk ditangani oleh tim kami, perusahaan dapat fokus mengembangkan usahanya.",
    "services.scope.s3Cta": "Diskusikan Layanan Akuntansi",
    "services.scope.s3Sub1Title": "Penyusunan Laporan Keuangan",
    "services.scope.s3Sub1Desc":
      "Penyusunan Laporan keuangan baik untuk PT, CV, perseorangan yang memiliki usaha UMKM, dll.",
    "services.scope.s3Sub2Title":
      "Jasa Penelaahan Akuntansi (Accounting Review)",
    "services.scope.s3Sub2Desc":
      "Meliputi pengidentifikasian dan verifikasi dokumen serta mensupervisi pencatatan transaksi dan laporan keuangan.",
    "services.scope.s3Sub3Title": "Internal Audit",
    "services.scope.s3Sub3Desc":
      "Pemeriksaan internal independen untuk memastikan kepatuhan prosedur, efektivitas pengendalian, dan keakuratan pelaporan keuangan.",
    "services.scope.s3Sub4Title": "Konsultasi Accounting Software",
    "services.scope.s3Sub4Desc":
      "Konsultasi dan rekomendasi pemilihan serta penerapan Accounting Software bagi perusahaan yang membutuhkan sistem pencatatan keuangan modern.",

    // Services Current Need
    "services.need.tag": "MULAI DARI KEBUTUHAN ANDA SAAT INI",
    "services.need.titleNormal": "Di mana bisnis Anda",
    "services.need.titleItalic": "membutuhkan kejelasan lebih?",
    "services.need.subtitle":
      "Pilih prioritas di bawah ini yang paling sesuai dengan kondisi perusahaan Anda. Kami merancang cakupan pendampingan sesuai kebutuhan bisnis Anda saat ini.",
    "services.need.n1Title": "Kepatuhan & Pelaporan Pajak",
    "services.need.n1Body":
      "Penuhi kewajiban pelaporan SPT Masa dan Tahunan secara akurat, tepat waktu, dan bebas dari kendala administrasi.",
    "services.need.n2Title": "Penggajian & Pajak PPh 21",
    "services.need.n2Body":
      "Kelola administrasi penggajian, pendaftaran BPJS, pemotongan PPh 21, dan e-Payslip secara terstruktur dan rahasia.",
    "services.need.n3Title": "Laporan Keuangan & Software",
    "services.need.n3Body":
      "Susun laporan keuangan rapi untuk PT, CV, maupun UMKM serta optimalkan penerapan software akuntansi Anda.",
    "services.need.n4Title": "Penelaahan & Pemeriksaan Pajak",
    "services.need.n4Body":
      "Ukur potensi risiko lewat penelaahan pajak dan dapatkan pendampingan penuh hingga selesai saat pemeriksaan pajak oleh fiskus.",

    // Services Overrides
    "services.ourApproach.tag": "CARA KAMI BEKERJA",
    "services.ourApproach.titlePrefix": "Dari ",
    "services.ourApproach.titleHighlight": "pemahaman ",
    "services.ourApproach.titleSuffix": "menuju langkah pasti.",
    "services.ourApproach.subtitle":
      "Mengelola dan mengembangkan bisnis Anda dengan pendampingan pajak dan keuangan yang tepat dimulai dari empat langkah jelas.",
    "services.cta.tag": "MULAI PERCAKAPAN",
    "services.cta.titleNormal":
      "Belum yakin layanan mana yang tepat untuk Anda?",
    "services.cta.description":
      "Beritahu kami tentang kondisi Anda saat ini. Kami akan membantu menentukan cakupan dukungan yang tepat untuk bisnis Anda.",
    "services.cta.buttonText": "Diskusikan Kebutuhan Anda",

    // Insights Hero
    "insight.hero.eyebrow": "AP CONSULTING · WAWASAN & ARTIKEL",
    "insight.hero.tag": "ARTIKEL UNGGULAN",
    "insight.hero.title": "Usaha Baru Buka, Harus Langsung Bayar Pajak?",
    "insight.hero.description":
      "Pajak merupakan bagian penting dari keberlanjutan ekonomi negara. Memahami kewajiban perpajakan sejak awal berdiri membantu usaha baru tetap patuh aturan dan terhindar dari sanksi administrasi.",
    "insight.hero.author": "Oleh Tim Redaksi AP Consulting",
    "insight.hero.date": "18 Desember 2023",
    "insight.hero.readTime": "Waktu baca 5 mnt",
    "insight.hero.readFeatured": "Baca artikel selengkapnya",

    // Insights Section
    "insight.articles.eyebrow": "ARTIKEL TERBARU",
    "insight.articles.titleBold": "Wawasan praktis",
    "insight.articles.titleItalic": "untuk keputusan pajak yang tepat.",
    "insight.articles.title": "Artikel & Wawasan Terbaru",
    "insight.articles.subtitle":
      "Dapatkan informasi terbaru mengenai regulasi perpajakan Indonesia, panduan kepatuhan, dan perspektif akuntansi praktis.",
    "insight.articles.readBtn": "Baca selengkapnya",
    "insight.articles.sidebarTitle": "Kategori Artikel",
    "insight.articles.showing": "Menampilkan halaman",
    "insight.articles.of": "dari",
    "insight.articles.articlesLabel": "Artikel",

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
    "insight.detail.share": "Bagikan",
    "insight.detail.linkCopied": "Link berhasil disalin",
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
    "contact.hero.tag": "HUBUNGI KAMI · AP CONSULTING",
    "contact.hero.titleLight": "Mari Terhubung ",
    "contact.hero.titleItalic": "dengan AP Consulting.",
    "contact.hero.headerSubtitle":
      "Ada pertanyaan? Jangan ragu untuk menghubungi kami.",
    "contact.hero.headerDesc":
      "Kirimkan pesan melalui formulir di bawah ini atau hubungi tim kami secara langsung melalui WhatsApp.",
    "contact.hero.cardTitle": "Hubungi Kami Langsung",
    "contact.hero.cardDesc":
      "Atau Anda dapat langsung menghubungi nomor WhatsApp kami di +62 857 5417 2217.",
    "contact.hero.whatsappCta": "Chat via WhatsApp",
    "contact.hero.emailLabel": "Email",
    "contact.hero.phoneLabel": "Telepon / WhatsApp",
    "contact.form.title": "Kirim pesan kepada kami",
    "contact.form.subtitle":
      "Bagikan sedikit informasi mengenai bisnis Anda, tantangan yang dihadapi, atau layanan yang ingin didiskusikan.",
    "contact.form.fullName": "Nama Lengkap",
    "contact.form.fullNamePlaceholder": "Masukkan nama Anda",
    "contact.form.email": "Alamat Email",
    "contact.form.emailPlaceholder": "nama@contoh.com",
    "contact.form.service": "Layanan yang Dibutuhkan",
    "contact.form.selectOne": "Pilih salah satu",
    "contact.form.optTax": "Layanan Perpajakan",
    "contact.form.optAcc": "Layanan Payroll Outsourcing",
    "contact.form.optBiz": "Layanan Akuntansi Pajak",
    "contact.form.optFin": "Pemeriksaan & Sengketa Pajak",
    "contact.form.optGen": "Pertanyaan Umum",
    "contact.form.phone": "Nomor Telepon (opsional)",
    "contact.form.company": "Nama Perusahaan (opsional)",
    "contact.form.companyPlaceholder": "contoh: PT Maju Bersama",
    "contact.form.message": "Pesan",
    "contact.form.messagePlaceholder":
      "Tuliskan bagaimana kami dapat membantu bisnis Anda",
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
    "contact.location.tag": "LOKASI KANTOR",
    "contact.location.titleNormal": "Kunjungi kantor",
    "contact.location.titleItalic": "Jakarta & Tangerang kami.",
    "contact.location.title": "Kunjungi Kantor Kami",
    "contact.location.address":
      "SUN Plaza, Jl. Meruya Ilir Raya No. 20, Jakarta Barat",
    "contact.location.openMaps": "Buka di Google Maps",
    "contact.location.loadMap": "Muat Peta Interaktif",

    // Contact CTA Overrides
    "contact.cta.tag": "MULAI PERCAKAPAN",
    "contact.cta.titleItalic": "Siap mengambil langkah berikutnya?",
    "contact.cta.description":
      "Diskusikan kebutuhan bisnis Anda dengan AP Consulting.",
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
