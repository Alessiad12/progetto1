const nodemailer = require('nodemailer');

// Configura il trasportatore (Gmail o un SMTP provider)
const transporter = nodemailer.createTransport({
  service: 'gmail',
  auth: {
    user: 'waderlust03@gmail.com',
    pass: 'cpcf vbmi ruqy bnxu',
  },
});

async function sendEmail({ to, subject, text, html }) {
  const mailOptions = {
    from: process.env.GMAIL_USER,
    to,
    subject,
    text,
    html,
  };

  return transporter.sendMail(mailOptions);
}

module.exports = { sendEmail };