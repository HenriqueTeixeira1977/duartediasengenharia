<?php

class PHP_Email_Form {
    public $to = '';
    public $from_name = '';
    public $from_email = '';
    public $subject = '';
    public $messages = [];
    public $smtp = [];
    public $ajax = false;

    public function add_message($content, $label, $priority = 0) {
        $this->messages[] = [
            'content' => $content,
            'label' => $label,
            'priority' => $priority
        ];
    }

    public function send() {
        if (empty($this->to)) {
            return json_encode(['status' => 'error', 'message' => 'Destinatário não definido']);
        }
        if (!filter_var($this->from_email, FILTER_VALIDATE_EMAIL)) {
            return json_encode(['status' => 'error', 'message' => 'E-mail inválido']);
        }

        $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Monta o corpo do e-mail
        $body = "Você recebeu uma nova mensagem do formulário de contato:\n\n";
        usort($this->messages, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        foreach ($this->messages as $msg) {
            $body .= "{$msg['label']}: {$msg['content']}\n";
        }

        // Se SMTP estiver configurado, usa PHPMailer
        if (!empty($this->smtp)) {
            return $this->send_smtp($body);
        }

        // Tenta enviar via função mail()
        if (mail($this->to, $this->subject, $body, $headers)) {
            return json_encode(['status' => 'success', 'message' => 'Mensagem enviada com sucesso!']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Falha ao enviar o e-mail']);
        }
    }

    private function send_smtp($body) {
        require __DIR__ . '/PHPMailer/PHPMailer.php';
        require __DIR__ . '/PHPMailer/SMTP.php';
        require __DIR__ . '/PHPMailer/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->smtp['username'];
        $mail->Password = $this->smtp['password'];
        $mail->Port = $this->smtp['port'];
        $mail->SMTPSecure = 'tls';

        $mail->setFrom($this->from_email, $this->from_name);
        $mail->addAddress($this->to);
        $mail->Subject = $this->subject;
        $mail->Body = $body;

        if ($mail->send()) {
            return json_encode(['status' => 'success', 'message' => 'Mensagem enviada com sucesso via SMTP!']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Erro SMTP: ' . $mail->ErrorInfo]);
        }
    }
}

?>
