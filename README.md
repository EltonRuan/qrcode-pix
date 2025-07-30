<div align='center'> <img style="width:100%" src="https://capsule-render.vercel.app/api?type=soft&height=200&color=FFFFFF&text=QR%20Code%20PIX%20Generator&fontSize=40&fontAlign=50&strokeWidth=0&descAlignY=80&stroke=000000"> </div> 

<nav align="center"> <h2>🔗 NAVIGATION</h2> <p> 
  <a href="#about-this-project">ABOUT THIS PROJECT</a> | <a href="#technologies-and-tools-used">TECHNOLOGIES AND TOOLS USED</a> | <a href="#how-it-works">HOW IT WORKS</a> | <a href="#final-considerations">FINAL CONSIDERATIONS</a> </p> 
</nav>

## ABOUT THIS PROJECT

The QR Code PIX Generator is a PHP-based application developed for the São Paulo Skills 2024 competition simulation. Its objective is to generate valid and scannable static PIX payment QR codes by simply filling out a form with key details like the receiver’s PIX key, amount, name, city, and an optional description.

The application demonstrates advanced PHP string manipulation, checksum calculations (CRC16), and QR code generation using Google Chart API.

## TECHNOLOGIES AND TOOLS USED

- PHP 7.4+

- HTML5

- CSS

- Google Chart API (for QR Code)

- Visual Studio Code

- XAMPP ( for local testing )

### STEP-BY-STEP
Clone the repository:

git clone https://github.com/EltonRuan/qrcodepix.git

Navigate to the project directory:

cd qrcodepix

Run the PHP server locally:

php -S localhost:8000

### Access the project:
Open your browser and navigate to:

http://localhost:8000/

## HOW IT WORKS

Form Fields

- Tipo de chave PIX: CPF, CNPJ, Telefone, E-mail, ou Aleatória.
- Chave: Valor da chave conforme o tipo.
- Valor: Valor da cobrança.
- Nome do recebedor: Máximo 25 caracteres.
- Cidade: Máximo 15 caracteres.
- Descrição (opcional): Até 20 caracteres.

## Backend Logic
Sanitiza e formata corretamente o tipo de chave (CPF, telefone com +55, etc.).

Calcula o CRC16 para gerar o código de pagamento estático PIX.

Retorna:

QR Code gerado com o link da Google Chart API.

Payload completo para uso em outros apps ou integração.

QR Code renderizado na tela.

Payload completo exibido em <textarea> para copiar.

Valor e chave retornados para conferência.


## FINAL CONSIDERATIONS

This documentation presents the QR Code PIX Generator, a practical application to reinforce your PHP skills while addressing real financial tech logic. It's an excellent example of:

How to work with raw data and string standards.

Understanding checksum generation for financial use (CRC16).

Integrating third-party services like QR Code APIs.

Feel free to expand the tool, apply visual enhancements with frameworks like TailwindCSS, or integrate it into an ecommerce checkout or invoice system.

Good luck on your journey—and congrats for getting here! 🚀

<p><strong>© 2025 EltonRuan. All rights reserved.</strong></p> 

<footer align="center"> <img style="width:100%" src="https://capsule-render.vercel.app/api?type=soft&height=20&color=FFFFFF&fontSize=50&fontAlign=50&strokeWidth=0&descAlignY=80&stroke=000000&reversal=false&section=footer"> </footer>
