<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MailSender extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
{
    $email = $this->from($this->data['fromEmail'], 'Cashpey')
          ->view($this->data['template'])
          ->subject($this->data['subject'])
          ->with([
              'mailData' => $this->data['templateData'],
              'repaymentScheduleSanction' => $this->data['repaymentScheduleSanction'] ?? null,
          ]);
    
    // Attach PDF if available
    if (!empty($this->data['pdf']) && !empty($this->data['pdfFileName'])) {
        Log::info('Attaching PDF: ' . $this->data['pdfFileName']);
        $email->attachData($this->data['pdf'], $this->data['pdfFileName'], [
            'mime' => 'application/pdf',
        ]);
    }

    // Attach eStamp document if available
    if (!empty($this->data['estampAttachment'])) {
        // Log eStamp content, name and mime type
        Log::info('Checking eStamp attachment...');
        
        $content = $this->data['estampAttachment']['content'] ?? null;
        $fileName = $this->data['estampAttachment']['name'] ?? null;
        $mimeType = $this->data['estampAttachment']['mime'] ?? null;

        // Log the extracted eStamp details
        Log::info('eStamp content: ' . (isset($content) ? 'Available' : 'Not Available'));
        Log::info('eStamp fileName: ' . (isset($fileName) ? $fileName : 'Not Available'));
        Log::info('eStamp mimeType: ' . (isset($mimeType) ? $mimeType : 'Not Available'));

        // If eStamp is valid, attach the file
        if ($content && $fileName && $mimeType) {
            Log::info('Attaching eStamp: ' . $fileName);
            $email->attachData($content, $fileName, [
                'mime' => $mimeType,
            ]);
        } else {
            Log::error('eStamp attachment is missing required fields: content, fileName, or mime type.');
        }
    } else {
        Log::info('No eStamp data found for attachment.');
    }

    return $email;
}


}