<div align='center'> <img style="width:100%" src="https://capsule-render.vercel.app/api?type=soft&height=200&color=FFFFFF&text=QR%20Code%20PIX%20Generator&fontSize=40&fontAlign=50&strokeWidth=0&descAlignY=80&stroke=000000"> </div> 

<nav align="center"> <h2>🔗 NAVIGATION</h2> <p> 
  <a href="#about-this-project">ABOUT THIS PROJECT</a> | <a href="#technologies-and-tools-used">TECHNOLOGIES AND TOOLS USED</a> | <a href="#how-it-works">HOW IT WORKS</a> | <a href="#final-considerations">FINAL CONSIDERATIONS</a> </p> 
</nav>

## ABOUT THIS PROJECT

The QR Code PIX Generator is a PHP-based application created out of personal interest and the desire to contribute to the developer community. Its main goal is to generate valid and scannable static PIX payment QR codes by simply filling out a form with essential information like the receiver’s PIX key, amount, name, city, and an optional description.

PIX is an instant payment system developed by the Central Bank of Brazil. It allows money transfers between individuals, companies, and the government to happen in real time, 24/7, with no intermediary bank fees. It has become one of the most popular and efficient payment methods in Brazil.

This tool was developed for study purposes, and aims to strengthen knowledge in PHP involving string manipulation, standardized data formatting, checksum generation (CRC16), and integration with external services like the Google Chart API for QR Code rendering.

It is designed for public use and can be easily adapted or implemented into other systems, such as invoicing, donation pages, ecommerce platforms, or personal finance apps.

## TECHNOLOGIES AND TOOLS USED

- PHP 7.4+
- HTML5
- CSS
- Google Chart API (for QR Code)
- Visual Studio Code
- XAMPP ( for local testing )

### STEP-BY-STEP

Clone the repository:

```bash
git clone https://github.com/EltonRuan/qrcodepix.git
```

Navigate to the project directory:

```bash
cd qrcodepix
```

Run the PHP server locally:

```bash
php -S localhost:8000
```

### Access the project:

Open your browser and navigate to:

```bash
http://localhost:8000/
```

## HOW IT WORKS

### Form Fields:

- PIX key type: CPF, CNPJ, Phone number, Email, or Random.
- Key: The key value according to the selected type.
- Amount: The payment amount.
- Receiver's name.
- City.
- Description (optional).

## Backend Logic

### Input Sanitization and Formatting

The application begins by receiving user input from a form, including the PIX key, value, receiver's name, city, and an optional description.
It sanitizes and formats the PIX key based on its type:

- CPF and CNPJ are stripped of all formatting characters.
- Phone numbers are automatically formatted to include the international prefix +55, as required by the PIX specification.
- Email and random keys are accepted as-is but still validated to remove leading/trailing whitespace.

### Payload Generation

Once all fields are processed, the application dynamically builds the PIX payload string according to the EMVCo and BACEN (Brazilian Central Bank) static QR code format specifications. This includes nested IDs for merchant account information, transaction value, country code, and more.

### Checksum Calculation (CRC16)

To ensure data integrity, the application calculates the CRC16-CCITT checksum at the end of the payload. This is a required step for any valid static PIX QR code and guarantees that the QR code can be validated by any compatible scanner or banking app.

### QR Code Generation

The final payload is passed to the Google Chart API, which returns a scannable QR Code image. This QR code represents the full payment data in a compact and scannable form.

### Output and Integration

After the QR code is generated, the following elements are returned and displayed:

- A QR code image, rendered on the screen, ready to be scanned.
- The full payload string, shown inside a <textarea> so the user can easily copy it for use in other systems (e.g., invoices, messaging apps, or back-end APIs).
- The formatted PIX key and amount are displayed for confirmation and transparency.

This flow ensures that all user input is safely processed and transformed into a valid static PIX payment code, ready for public use or system integration.

## FINAL CONSIDERATIONS

This documentation presents the QR Code PIX Generator, a practical application to reinforce your PHP skills while addressing real financial tech logic. It's an excellent example of:

How to work with raw data and string standards.

Understanding checksum generation for financial use (CRC16).

Integrating third-party services like QR Code APIs.

Feel free to expand the tool, apply visual enhancements with frameworks like TailwindCSS, or integrate it into an ecommerce checkout or invoice system.

Good luck on your journey—and congrats for getting here! 🚀

<p><strong>© 2025 EltonRuan. All rights reserved.</strong></p> 

<footer align="center"> <img style="width:100%" src="https://capsule-render.vercel.app/api?type=soft&height=20&color=FFFFFF&fontSize=50&fontAlign=50&strokeWidth=0&descAlignY=80&stroke=000000&reversal=false&section=footer"> </footer>
