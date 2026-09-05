CampusHub replaces manual spreadsheet and email-based coordination with a single database-driven platform. Built with PHP, MySQL, HTML5, CSS, and XML, it provides self-service access for students and full administrative control.

✨ Features
For Students
🔐 User registration and login

📅 View and register for upcoming events

👤 Manage personal profile (institution, club memberships)

🖼️ Upload profile photos

📸 View multimedia content (images, video, audio) linked to events

📢 Read announcements

For Administrators
🛡️ Secure admin panel with role-based access

👥 Full CRUD for student records

📆 Create, edit, and delete events

📋 Manage event registrations

📰 Publish announcements

🎨 Upload event posters and media (images, video, audio)

📤 Export event data as XML

🛠️ Technologies
Backend: PHP

Database: MySQL

Frontend: HTML5, CSS3 (with custom properties for theming)

XML: SimpleXML for data export

Authentication: PHP Sessions

📁 Database Structure
The system uses 10 interconnected tables:

institutions

students

admins

clubs

event_categories

events

registrations

student_clubs (junction table for many-to-many)

announcements

media

🚀 Installation
Prerequisites
PHP 7.4+

MySQL 5.7+

Web server (Apache/Nginx)


