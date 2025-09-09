# JobPortal+

A full-featured, web-based job portal platform that connects job seekers with employers. Built with a focus on simplicity and functionality.

## 🚀 Live Demo
https://jobportalplus.lovestoblog.com/job-portal2

## ✨ Features

### For Job Seekers
- **User Registration & Authentication:** Secure login system for job seekers.
- **Profile & CV Management:** Create, view, and update a professional digital CV.
- **Browse Jobs:** View all available job postings with filtering options.
- **Apply for Jobs:** Easy application process with CV upload.
- **Application Tracking:** Dashboard to track the status of all applications (e.g., Applied, Selected).
- **Withdraw Applications:** Option to withdraw applications from the dashboard.

### For Employers
- **Employer Registration:** Separate registration flow for companies.
- **Post Jobs:** Intuitive form to create and publish new job listings.
- **Manage Jobs:** Dashboard to view all active job postings.
- **View Applicants:** See all applicants for each posted job.
- **Application Management:** Track and manage candidate statuses (e.g., mark as Selected).

### General
- **Fully Responsive Design:** Works seamlessly on desktop, tablet, and mobile devices.
- **Clean UI/UX:** Built with Bootstrap for a modern and professional look.
- **Dynamic Content:** All data is dynamically loaded from the database.

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend:** PHP
- **Database:** MySQL
- **Server:** XAMPP / WAMP / LAMP (Apache, MySQL)

## 📦 Installation & Setup

To run this project locally, follow these steps:

1.  **Prerequisites:**
    - Ensure you have a local web server stack installed (e.g., [XAMPP](https://www.apachefriends.org/), [WAMP](https://www.wampserver.com/), or [MAMP](https://www.mamp.info/)).

2.  **Clone/Download the Project:**
    - Download the source code and extract it into your web server's root directory (e.g., `xampp/htdocs/`).
    - Rename the folder to `job-portal2` or something meaningful.

3.  **Database Setup:**
    - Start your Apache and MySQL services from your server stack control panel.
    - Open phpMyAdmin (usually at `http://localhost/phpmyadmin`).
    - Create a new database named `job-portal` (or any name you prefer).
    - Import the provided SQL file (e.g., `database/job-portal.sql`) into your new database to create the necessary tables.

4.  **Configuration:**
    - Locate the PHP configuration file (often named `config.php` or `database.php` in the project's root or an `includes/` folder).
    - Update the database connection variables with your local credentials:
        `$host = 'localhost';`
        `$dbname = 'job-portal';`
        `$username = 'root'; // Default XAMPP username`
        `$password = ''; // Default XAMPP password is empty`

5.  **Run the Application:**
    - Open your web browser and go to `http://localhost/job-portal2` (or the name of your project folder).
    - You should see the JobPortal+ homepage. Register as a new user or employer to start testing.


## 🔐 Default Admin/Test Accounts

*It is highly recommended to change these passwords after installation!*

**Job Seeker Test Account:**
- UserName : shah
- Password: halabrazil

**Employer Test Account:**
- Email: mugdho
- Password: halabrazil


## 📄 License

This project is created for educational and portfolio purposes.

## 👨‍💻 Developer

**Shahmaruf Siraj Mugdho**
- Email: shahmarufsiraji@gmail.com

## 🤝 Contributing

This is a personal portfolio project. Contributions are not expected but feedback is always welcome!


**Thank you for checking out JobPortal+!**
