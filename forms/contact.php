<?php
  /**
  * Requires the "PHP Email Form" library
  * The "PHP Email Form" library is available only in the pro version of the template
  * The library should be uploaded to: vendor/php-email-form/php-email-form.php
  * For more info and help: https://bootstrapmade.com/php-email-form/
  */

  // Replace contact@example.com with your real receiving email address
  $receiving_email_address = 'herniqueteixeira.wd@gmail.com';

  if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

  $contact = new PHP_Email_Form;
  $contact->ajax = true;
  
  $contact->to = $receiving_email_address;
  $contact->from_name = $_POST['name'];
  $contact->from_email = $_POST['email'];
  $contact->subject = $_POST['subject'];

  // Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
  
  $contact->smtp = array(
    'host' => 'smtp.gmail.com',
    'username' => 'herniqueteixeira.wd@gmail.com',
    'password' => 'Rick1977',
    'port' => '587'
  );


  $contact->add_message( $_POST['name'], 'From');
  $contact->add_message( $_POST['email'], 'Email');
  $contact->add_message( $_POST['message'], 'Message', 10);

  echo $contact->send();
?>

<!--  Conecção com o banco de dados  -->
      
<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  // Configurações do banco de dados
  $host = 'localhost';
  $dbname = 'duartediasengenharia';
  $username = 'root';  // Substitua se necessário
  $password = '';      // Substitua se necessário

  // Conectar ao MySQL
  $conn = new mysqli($host, $username, $password, $dbname);

  // Verificar conexão
  if ($conn->connect_error) {
      die("Falha na conexão com o banco de dados: " . $conn->connect_error);
  }

  // Receber os dados do formulário
  $nome = $_POST['name'];
  $email = $_POST['email'];
  $assunto = $_POST['subject'];
  $mensagem = $_POST['message'];

  // Inserir os dados no banco de dados
  $sql = "INSERT INTO contatos (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssss", $nome, $email, $assunto, $mensagem);

  if ($stmt->execute()) {
      echo "Dados salvos com sucesso!";
  } else {
      echo "Erro ao salvar os dados: " . $conn->error;
  }

  // Fechar conexão com o banco de dados
  $stmt->close();
  $conn->close();

  // Configuração do PHPMailer
  require '../assets/vendor/php-email-form/PHPMailer/PHPMailer.php';
  require '../assets/vendor/php-email-form/PHPMailer/SMTP.php';
  require '../assets/vendor/php-email-form/PHPMailer/Exception.php';

  $mail = new PHPMailer(true);

  try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com'; // Servidor SMTP
      $mail->SMTPAuth = true;
      $mail->Username = 'henriqueteixeira.wd@gmail.com'; // Seu e-mail SMTP
      $mail->Password = 'Rick1977'; // Senha ou senha de app do Gmail
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      // Configuração do e-mail
      $mail->setFrom($email, $nome);
      $mail->addAddress('contato@henriqueteixeiraoficial.com.br'); // E-mail do destinatário
      $mail->Subject = $assunto;
      $mail->Body = "Nome: $nome\nE-mail: $email\n\nMensagem:\n$mensagem";

      // Enviar e-mail
      if ($mail->send()) {
          echo "E-mail enviado com sucesso!";
      } else {
          echo "Erro ao enviar e-mail.";
      }
  } catch (Exception $e) {
      echo "Erro: {$mail->ErrorInfo}";
  }
?>
