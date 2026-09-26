<?php
/**
 * VOSTOKPRIBOR Bilingual Translation Engine (English & Arabic)
 * Location: api/helpers/I18n.php
 */
class I18n {
    private static $dictionary = [
        'en' => [
            // Statuses
            'Active'      => 'Active',
            'Pending'     => 'Pending',
            'Paid'        => 'Paid',
            'Overdue'     => 'Overdue',
            'Processing'  => 'Processing',
            'Shipped'     => 'Shipped',
            'Delivered'   => 'Delivered',
            'Cancelled'   => 'Cancelled',
            'Execution'   => 'Execution',
            'Integration' => 'Integration',
            'Design'      => 'Design',
            'Testing'     => 'Testing',
            'Planning'    => 'Planning',
            'Procurement' => 'Procurement',
            'Maintenance' => 'Maintenance',
            'ContractReview' => 'Contract Review',
            'Resolved'    => 'Resolved',
            'Open'        => 'Open',
            'InProgress'  => 'In Progress',
            'Investigating' => 'Investigating',
            'Escalated'   => 'Escalated',
            'Proposal'    => 'Proposal',
            'Negotiation' => 'Negotiation',
            'Qualification' => 'Qualification',
            'Won'         => 'Won',
            'Lost'        => 'Lost',
            'New'         => 'New',
            'Qualified'   => 'Qualified',
            'Converted'   => 'Converted',
            'Rejected'    => 'Rejected',
            'Approved'    => 'Approved',

            // Billing Models
            'PerUnit'             => 'Per Unit',
            'PerProject'          => 'Per Project',
            'SubscriptionMonthly' => 'Monthly Subscription',
            'SubscriptionAnnual'  => 'Annual Subscription',
            'AnnualContract'      => 'Annual Contract',

            // Departments
            'EXE' => 'Executive Management',
            'SAL' => 'Sales & Commercial Affairs',
            'OPS' => 'Operations & Logistics',
            'ENG' => 'Engineering & Automation',
            'FIN' => 'Finance & Billing',
            'HRA' => 'Human Resources & Admin',
            'ITD' => 'Information Technology',
            'GOV' => 'Governance, Risk & Compliance'
        ],
        'ar' => [
            // الحالات
            'Active'      => 'نشط',
            'Pending'     => 'قيد الانتظار',
            'Paid'        => 'مدفوعة',
            'Overdue'     => 'متأخرة',
            'Processing'  => 'قيد المعالجة',
            'Shipped'     => 'تم الشحن',
            'Delivered'   => 'تم التسليم',
            'Cancelled'   => 'ملغى',
            'Execution'   => 'قيد التنفيذ',
            'Integration' => 'مرحلة التكامل',
            'Design'      => 'مرحلة التصميم',
            'Testing'     => 'مرحلة الاختبار',
            'Planning'    => 'مرحلة التخطيط',
            'Procurement' => 'المشتريات والتوريد',
            'Maintenance' => 'الصيانة والدعم',
            'ContractReview' => 'مراجعة العقد',
            'Resolved'    => 'تم الحل بنجاح',
            'Open'        => 'مفتوحة',
            'InProgress'  => 'قيد المعالجة',
            'Investigating' => 'قيد التحقيق الفني',
            'Escalated'   => 'تم التصعيد',
            'Proposal'    => 'تقديم العرض التجاري',
            'Negotiation' => 'مرحلة التفاوض',
            'Qualification' => 'تأهيل العميل',
            'Won'         => 'تم الفوز بالعقد',
            'Lost'        => 'ملغاة / خسارة',
            'New'         => 'جديد',
            'Qualified'   => 'مؤهل',
            'Converted'   => 'تم التحويل',
            'Rejected'    => 'مرفوض',
            'Approved'    => 'معتمد',

            // نماذج الفوترة
            'PerUnit'             => 'لكل وحدة',
            'PerProject'          => 'حسب المشروع',
            'SubscriptionMonthly' => 'اشتراك شهري',
            'SubscriptionAnnual'  => 'اشتراك سنوي',
            'AnnualContract'      => 'عقد سنوي',

            // الأقسام
            'EXE' => 'الإدارة التنفيذية',
            'SAL' => 'المبيعات والشؤون التجارية',
            'OPS' => 'العمليات والخدمات اللوجستية',
            'ENG' => 'الهندسة والأتمتة',
            'FIN' => 'المالية والفوترة',
            'HRA' => 'الموارد البشرية والإدارة',
            'ITD' => 'تكنولوجيا المعلومات',
            'GOV' => 'الحوكمة وإدارة المخاطر والامتثال'
        ]
    ];

    public static function translate($key, $lang = 'en') {
        $lang = strtolower(substr($lang, 0, 2)) === 'ar' ? 'ar' : 'en';
        return self::$dictionary[$lang][$key] ?? $key;
    }
}
