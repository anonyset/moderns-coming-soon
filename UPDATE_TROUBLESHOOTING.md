# راهنمای حل مشکل "Unable to rename the update"

## خطای رخ‌داده
```
Unable to rename the update to match the existing directory.
```

وردپرس نتوانسته پوشهٔ ZIP استخراج‌شده (مثلاً `anonyset-modern-coming-soon-0dfd1c1`) را به نام پوشهٔ پلاگین (`modern-coming-soon`) تغییر نام دهد.

---

## علل و راه‌حل‌ها

### ۱. مشکل مجوزها (Permissions)
**علت:** کاربر وب‌سرور (www-data یا IUSR) اجازهٔ نوشتن/حذف در پوشهٔ پلاگین را ندارد.

**راه‌حل:**
```bash
# در سرور (SSH):
cd /path/to/wordpress/wp-content/plugins
chmod -R 755 modern-coming-soon
chown -R www-data:www-data modern-coming-soon  # یا کاربر وب‌سرور شما

# در ویندوز (PowerShell - اجرا‌کننده):
icacls "C:\path\to\plugins\modern-coming-soon" /grant "IIS_IUSRS:(OI)(CI)F" /T

# یا از داشبورد:
# wp-content و wp-content/plugins باید نوشت‌پذیری داشته باشند
```

---

### ۲. فایل‌های قفل‌شده
**علت:** فایل‌های پلاگین توسط PHP/فرآیند قفل شده‌اند یا توسط آنتی‌ویروس/ایدیتور.

**راه‌حل:**
- در داشبورد وردپرس پلاگین را ابتدا **غیرفعال** کنید
- سپس آپدیت را اجرا کنید
- پس از موفقیت، دوباره **فعال** کنید

---

### ۳. حل فوری (دستی)
اگر خطا همچنان ادامه یافت:

#### گزینهٔ A: دستی پاک و آپلود
1. در داشبورد: **Plugins** → **Modern Coming Soon** → **Delete** (حذف پلاگین)
2. در `wp-content/plugins` اطمینان‌یابید پوشهٔ `modern-coming-soon` حذف شده
3. **Plugins** → **Add New** → **Upload Plugin**
4. از GitHub دانلود کنید: https://github.com/anonyset/modern-coming-soon/archive/main.zip
   - یا از **Releases**: https://github.com/anonyset/modern-coming-soon/releases
5. ZIP را آپلود و فعال کنید

#### گزینهٔ B: از طریق SFTP/FTP
1. SFTP/FTP کلاینت اتصال برقرار کنید
2. پوشهٔ `modern-coming-soon` را پاک کنید
3. ZIP جدید را دانلود و اکسترکت کنید در مسیر `wp-content/plugins/`
   - نام پوشه باید دقیقاً `modern-coming-soon` باشد
4. در داشبورد **Plugins** فعال کنید

#### گزینهٔ C: از طریق WP-CLI
```bash
# در سرور (SSH):
wp plugin delete modern-coming-soon

# سپس نسخهٔ جدید را آپلود کنید یا:
cd /path/to/wordpress/wp-content/plugins
wget https://github.com/anonyset/modern-coming-soon/archive/main.zip
unzip main.zip
mv anonyset-modern-coming-soon-* modern-coming-soon
rm main.zip

# سپس فعال کنید:
wp plugin activate modern-coming-soon
```

---

### ۴. تفعیل لاگ برای دیباگ
اگر می‌خواهید جزئیات بیشتر ببینید:

در `wp-config.php` این خطوط را اضافه کنید:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

سپس آپدیت را دوباره اجرا کنید و فایل `wp-content/debug.log` را نگاه کنید.

---

## نتیجهٔ آخر
- برای **آپدیت خودکار موثر** در آینده: مطمئن شوید مجوزهای پوشهٔ پلاگین درست است
- اگر مشکل حل نشد: از روش دستی (A یا B) استفاده کنید
- اگر همچنان خطا دارید: لاگ را بررسی کنید و تماس بگیرید

---

## نکته برای توسعه‌دهنده
کد پلاگین fallback‌های اضافی دارد (copy/rename) که در `includes/class-mcs-updater.php` موجود است.
اگر این fallback‌ها فعال شوند (پس از deploy نسخهٔ تازه)، احتمال موفقیت آپدیت افزایش می‌یابد.
