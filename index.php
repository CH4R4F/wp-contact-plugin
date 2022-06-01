<?php
  /**
   * Plugin Name:       wpcontact
   * Plugin URI:        https://example.com/plugins/the-basics/
   * Description:       this plugin creates a contact form in cntact-us pages and show the data collected from the form in the dashboard 
   * Version:           0.0.1
   * Requires at least: 5.2
   * Requires PHP:      7.2
   * Author:            CMarghin
   * Author URI:        https://author.example.com/
   * License:           GPL v2 or later
   * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
   * Update URI:        https://example.com/my-plugin/
   * Text Domain:       wp-contact
  **/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

if(! defined('ABSPATH')) {
  exit;
}


// you should use tailwindcss to see the styles
function createForm() {
  $form = <<<FORM
    <form method='post'>
      <div class="mb-6">
        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Your Name</label>
        <input name="user_name" type="text" id="name" class="shadow-sm border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder="john doe" required>
      </div>
      <div class="mb-6">
        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Your email</label>
        <input name="user_email" type="email" id="email" class="shadow-sm border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder="name@flowbite.com" required>
      </div>
      <div class="mb-6">
        <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Subject</label>
        <input name="user_subject" type="text" id="subject" class="shadow-sm border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder="Your Subject" required>
      </div>
      <div class="mb-6">
        <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Your message</label>
        <textarea name="user_message" id="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Leave a comment..." required></textarea>
      </div>

      <input name="send_message" type="submit" class="p-4 bg-[#1bae70]" value="Send" />
    </form>
  FORM;

  return $form;
}
// use this shortcode to show the form
add_shortcode('contact_form', 'createForm');

function send_email() {
  if(isset($_POST['send_message'])) {
    $name = sanitize_text_field($_POST['user_name']);
    $email = sanitize_email($_POST['user_email']);
    $subject = sanitize_text_field($_POST['user_subject']);
    $message = sanitize_textarea_field($_POST['user_message']);
    
    $subject = "New Message from $name - $email about $subject";
    

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 2;                                       //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to gmail smtp
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = '';           //the email that will be used to send the email
        $mail->Password   = '';                     //the password of the email above
        $mail->SMTPSecure = "tls";                                  //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('');           //the email that you want to send the email to

        //Content
        $mail->isHTML(true);                                         //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->SMTPDebug  = SMTP::DEBUG_OFF;
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
}

add_action('wp_head', 'send_email');

?>