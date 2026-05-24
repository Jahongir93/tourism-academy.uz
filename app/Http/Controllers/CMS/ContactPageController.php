<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsContent;

class ContactPageController extends Controller
{
    public function index()
    {
        $defaultContact = [
            // Page title
            ['key' => 'contact_page_title', 'type' => 'text', 'value_uz' => "Bog'lanish", 'value_en' => 'Contact', 'value_ru' => 'Контакты', 'order' => 1],

            // Hero section
            ['key' => 'contact_hero_badge', 'type' => 'text', 'value_uz' => "BOG'LANISH", 'value_en' => 'CONTACT', 'value_ru' => 'СВЯЗЬ', 'order' => 2],
            ['key' => 'contact_hero_title', 'type' => 'text', 'value_uz' => "Biz Bilan Bog'laning", 'value_en' => 'Get In Touch With Us', 'value_ru' => 'Свяжитесь с нами', 'order' => 3],
            ['key' => 'contact_hero_subtitle', 'type' => 'textarea', 'value_uz' => 'Savollaringiz bormi? Biz doimo aloqadamiz va sizga yordam berishga tayyormiz!', 'value_en' => 'Do you have questions? We are always in touch and ready to help you!', 'value_ru' => 'Есть вопросы? Мы всегда на связи и готовы помочь вам!', 'order' => 4],

            // Breadcrumb
            ['key' => 'contact_breadcrumb_home', 'type' => 'text', 'value_uz' => 'Bosh sahifa', 'value_en' => 'Home', 'value_ru' => 'Главная', 'order' => 5],
            ['key' => 'contact_breadcrumb_contact', 'type' => 'text', 'value_uz' => "Bog'lanish", 'value_en' => 'Contact', 'value_ru' => 'Контакты', 'order' => 6],

            // Address
            ['key' => 'contact_address_title', 'type' => 'text', 'value_uz' => 'Manzil', 'value_en' => 'Address', 'value_ru' => 'Адрес', 'order' => 10],
            ['key' => 'contact_address_text', 'type' => 'textarea', 'value_uz' => "Samarqand shahar,<br>Istiqlol ko'chasi, 47", 'value_en' => 'Samarkand city,<br>Istiqlol street, 47', 'value_ru' => 'г. Самарканд,<br>ул. Истиклол, 47', 'order' => 11],

            // Phone
            ['key' => 'contact_phone_title', 'type' => 'text', 'value_uz' => 'Telefon', 'value_en' => 'Phone', 'value_ru' => 'Телефон', 'order' => 12],
            ['key' => 'contact_phone_text', 'type' => 'textarea', 'value_uz' => '+998 66 233 XX XX<br>+998 66 233 XX XX', 'value_en' => '+998 66 233 XX XX<br>+998 66 233 XX XX', 'value_ru' => '+998 66 233 XX XX<br>+998 66 233 XX XX', 'order' => 13],

            // Email
            ['key' => 'contact_email_title', 'type' => 'text', 'value_uz' => 'Email', 'value_en' => 'Email', 'value_ru' => 'Email', 'order' => 14],
            ['key' => 'contact_email_text', 'type' => 'textarea', 'value_uz' => 'info@tourism.uz<br>admin@tourism.uz', 'value_en' => 'info@tourism.uz<br>admin@tourism.uz', 'value_ru' => 'info@tourism.uz<br>admin@tourism.uz', 'order' => 15],

            // Working hours
            ['key' => 'contact_hours_title', 'type' => 'text', 'value_uz' => 'Ish vaqti', 'value_en' => 'Working Hours', 'value_ru' => 'Рабочее время', 'order' => 20],
            ['key' => 'contact_hours_weekdays', 'type' => 'text', 'value_uz' => 'Dushanba - Juma', 'value_en' => 'Monday - Friday', 'value_ru' => 'Понедельник - Пятница', 'order' => 21],
            ['key' => 'contact_hours_weekdays_time', 'type' => 'text', 'value_uz' => '9:00 - 18:00', 'value_en' => '9:00 - 18:00', 'value_ru' => '9:00 - 18:00', 'order' => 22],
            ['key' => 'contact_hours_saturday', 'type' => 'text', 'value_uz' => 'Shanba', 'value_en' => 'Saturday', 'value_ru' => 'Суббота', 'order' => 23],
            ['key' => 'contact_hours_saturday_time', 'type' => 'text', 'value_uz' => '9:00 - 14:00', 'value_en' => '9:00 - 14:00', 'value_ru' => '9:00 - 14:00', 'order' => 24],
            ['key' => 'contact_hours_sunday', 'type' => 'text', 'value_uz' => 'Yakshanba', 'value_en' => 'Sunday', 'value_ru' => 'Воскресенье', 'order' => 25],
            ['key' => 'contact_hours_closed', 'type' => 'text', 'value_uz' => 'Dam olish', 'value_en' => 'Closed', 'value_ru' => 'Выходной', 'order' => 26],

            // Social
            ['key' => 'contact_social_title', 'type' => 'text', 'value_uz' => 'Ijtimoiy tarmoqlar', 'value_en' => 'Social Networks', 'value_ru' => 'Социальные сети', 'order' => 30],

            // Contact form
            ['key' => 'contact_form_title', 'type' => 'text', 'value_uz' => 'Xabar yuborish', 'value_en' => 'Send Message', 'value_ru' => 'Отправить сообщение', 'order' => 40],
            ['key' => 'contact_form_subtitle', 'type' => 'textarea', 'value_uz' => 'Formani to\'ldirib yuboring', 'value_en' => 'Fill out the form and send', 'value_ru' => 'Заполните форму и отправьте', 'order' => 41],
            ['key' => 'contact_form_name', 'type' => 'text', 'value_uz' => 'Ismingiz', 'value_en' => 'Your Name', 'value_ru' => 'Ваше имя', 'order' => 42],
            ['key' => 'contact_form_name_placeholder', 'type' => 'text', 'value_uz' => 'Ismingizni kiriting', 'value_en' => 'Enter your name', 'value_ru' => 'Введите ваше имя', 'order' => 43],
            ['key' => 'contact_form_phone', 'type' => 'text', 'value_uz' => 'Telefon', 'value_en' => 'Phone', 'value_ru' => 'Телефон', 'order' => 44],
            ['key' => 'contact_form_email', 'type' => 'text', 'value_uz' => 'Email', 'value_en' => 'Email', 'value_ru' => 'Email', 'order' => 45],
            ['key' => 'contact_form_subject', 'type' => 'text', 'value_uz' => 'Mavzu', 'value_en' => 'Subject', 'value_ru' => 'Тема', 'order' => 46],
            ['key' => 'contact_form_select', 'type' => 'text', 'value_uz' => 'Mavzuni tanlang', 'value_en' => 'Select a subject', 'value_ru' => 'Выберите тему', 'order' => 47],
            ['key' => 'contact_form_subject_admission', 'type' => 'text', 'value_uz' => 'Qabul', 'value_en' => 'Admission', 'value_ru' => 'Приём', 'order' => 48],
            ['key' => 'contact_form_subject_education', 'type' => 'text', 'value_uz' => "Ta'lim", 'value_en' => 'Education', 'value_ru' => 'Образование', 'order' => 49],
            ['key' => 'contact_form_subject_general', 'type' => 'text', 'value_uz' => 'Umumiy', 'value_en' => 'General', 'value_ru' => 'Общий', 'order' => 50],
            ['key' => 'contact_form_subject_partnership', 'type' => 'text', 'value_uz' => 'Hamkorlik', 'value_en' => 'Partnership', 'value_ru' => 'Партнёрство', 'order' => 51],
            ['key' => 'contact_form_subject_other', 'type' => 'text', 'value_uz' => 'Boshqa', 'value_en' => 'Other', 'value_ru' => 'Другое', 'order' => 52],
            ['key' => 'contact_form_message', 'type' => 'text', 'value_uz' => 'Xabar', 'value_en' => 'Message', 'value_ru' => 'Сообщение', 'order' => 53],
            ['key' => 'contact_form_message_placeholder', 'type' => 'text', 'value_uz' => 'Xabaringizni yozing...', 'value_en' => 'Write your message...', 'value_ru' => 'Напишите ваше сообщение...', 'order' => 54],
            ['key' => 'contact_form_submit', 'type' => 'text', 'value_uz' => 'Yuborish', 'value_en' => 'Send', 'value_ru' => 'Отправить', 'order' => 55],

            // FAQ
            ['key' => 'contact_faq_badge', 'type' => 'text', 'value_uz' => 'FAQ', 'value_en' => 'FAQ', 'value_ru' => 'FAQ', 'order' => 60],
            ['key' => 'contact_faq_title', 'type' => 'text', 'value_uz' => "Ko'p so'raladigan savollar", 'value_en' => 'Frequently Asked Questions', 'value_ru' => 'Часто задаваемые вопросы', 'order' => 61],
            ['key' => 'contact_faq1_question', 'type' => 'text', 'value_uz' => 'Qabul qanday boshlanadi?', 'value_en' => 'How does admission start?', 'value_ru' => 'Как начинается приём?', 'order' => 62],
            ['key' => 'contact_faq1_answer', 'type' => 'textarea', 'value_uz' => 'Qabul har yili avgust oyida boshlanadi.', 'value_en' => 'Admission starts every year in August.', 'value_ru' => 'Приём начинается ежегодно в августе.', 'order' => 63],
            ['key' => 'contact_faq2_question', 'type' => 'text', 'value_uz' => 'Qanday hujjatlar kerak?', 'value_en' => 'What documents are required?', 'value_ru' => 'Какие документы нужны?', 'order' => 64],
            ['key' => 'contact_faq2_answer', 'type' => 'textarea', 'value_uz' => 'Pasport, maktab diplomi va ariza kerak.', 'value_en' => 'Passport, school diploma and application are required.', 'value_ru' => 'Требуются паспорт, школьный диплом и заявление.', 'order' => 65],
            ['key' => 'contact_faq3_question', 'type' => 'text', 'value_uz' => 'Stipendiya bormi?', 'value_en' => 'Is there a scholarship?', 'value_ru' => 'Есть ли стипендия?', 'order' => 66],
            ['key' => 'contact_faq3_answer', 'type' => 'textarea', 'value_uz' => "A'lo baholar uchun stipendiya taqdim etiladi.", 'value_en' => 'Scholarship is provided for excellent grades.', 'value_ru' => 'Стипендия предоставляется за отличные оценки.', 'order' => 67],
            ['key' => 'contact_faq4_question', 'type' => 'text', 'value_uz' => 'Yotoqxona mavjudmi?', 'value_en' => 'Is there a dormitory?', 'value_ru' => 'Есть ли общежитие?', 'order' => 68],
            ['key' => 'contact_faq4_answer', 'type' => 'textarea', 'value_uz' => 'Ha, zamonaviy yotoqxonalar mavjud.', 'value_en' => 'Yes, modern dormitories are available.', 'value_ru' => 'Да, имеются современные общежития.', 'order' => 69],
        ];

        foreach ($defaultContact as $item) {
            CmsContent::firstOrCreate(
                ['section' => 'contact', 'key' => $item['key']],
                [
                    'type' => $item['type'],
                    'value_uz' => $item['value_uz'],
                    'value_en' => $item['value_en'],
                    'value_ru' => $item['value_ru'],
                    'order' => $item['order']
                ]
            );
        }

        $contents = CmsContent::where('section', 'contact')->orderBy('order')->get();

        return view('cms.contact.index', compact('contents'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $values) {
            if (is_array($values)) {
                $existing = CmsContent::where('section', 'contact')->where('key', $key)->first();

                $updateData = [
                    'section' => 'contact',
                    'key' => $key,
                    'type' => $existing->type ?? 'text',
                ];

                if (array_key_exists('uz', $values)) {
                    $updateData['value_uz'] = $values['uz'] ?? '';
                }
                if (array_key_exists('en', $values)) {
                    $updateData['value_en'] = $values['en'] ?? '';
                }
                if (array_key_exists('ru', $values)) {
                    $updateData['value_ru'] = $values['ru'] ?? '';
                }

                if ($existing) {
                    $existing->update($updateData);
                } else {
                    CmsContent::create($updateData);
                }
            }
        }

        return redirect()->route('cms.contact.index')->with('success', "Aloqa sahifasi muvaffaqiyatli yangilandi!");
    }
}
