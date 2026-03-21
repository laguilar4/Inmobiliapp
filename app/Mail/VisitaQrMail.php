<?php

namespace App\Mail;

use App\Models\VisitaCabecera;
use App\Models\VisitaCuerpo;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VisitaQrMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $qrBase64;

    public function __construct(
        public VisitaCuerpo $visitante,
        public VisitaCabecera $cabecera,
    ) {
        $data = json_encode([
            'nombre'        => $visitante->nombre,
            'cedula'        => $visitante->cedula,
            'correo'        => $visitante->correo,
            'proyecto'      => $cabecera->proyecto->nombre ?? '',
            'fecha_inicio'  => $cabecera->fecha_inicio->format('d/m/Y H:i'),
            'fecha_fin'     => $cabecera->fecha_fin->format('d/m/Y H:i'),
        ]);

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $svg = (new Writer($renderer))->writeString($data);

        $this->qrBase64 = base64_encode($svg);
    }

    public function build(): self
    {
        return $this->subject('Código QR para visita - ' . ($this->cabecera->proyecto->nombre ?? ''))
            ->view('emails.visita_qr');
    }
}
