<?php
require('template/assets/vendor/fpdf/fpdf.php');

// Classe personalizada do FPDF
class PDF extends FPDF
{
    // Definir cores personalizadas
    function SetHeaderColor()
    {
        $this->SetFillColor(255, 140, 0); // Laranja
        $this->SetTextColor(255, 255, 255); // Branco
    }

    function SetContentColor()
    {
        $this->SetTextColor(0, 0, 0); // Preto
    }

    // Cabeçalho da página
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->SetHeaderColor();
        $this->Cell(0, 15, 'Informacoes de Alocacao', 0, 1, 'C', true);
        $this->Ln(10); // Espaço após o título
    }

    // Rodapé da página
    function Footer()
    {
        // Posição a 1,5 cm da parte inferior
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetContentColor();
        // Número da página
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }

    // Função para adicionar seções com estilo
    function AddSectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(230, 230, 230); // Cinza claro
        $this->Cell(0, 10, utf8_decode($title), 0, 1, 'L', true);
        $this->Ln(2);
    }

    // Função para adicionar texto estilizado
    function AddText($label, $value)
    {
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 10, utf8_decode("$label:"), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, utf8_decode($value), 0, 1, 'L');
        $this->Ln(1); // Pequeno espaço entre as linhas
    }

    // Função para adicionar blocos de texto
    function AddBlock($title, $content)
    {
        $this->AddSectionTitle($title);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 8, utf8_decode($content), 0, 'L');
        $this->Ln(4); // Espaço após o bloco
    }
}

// Criando o PDF
$pdf = new PDF();
$pdf->AddPage();

// Definir cor padrão para conteúdo
$pdf->SetContentColor();

// Informações de Alocação
$pdf->AddSectionTitle('Local');
$pdf->AddText('Nome', 'ESCOLA DE EDUCAÇÃO INFANTIL WILLY VIANA DAS NEVES');
$pdf->AddText('Zona de alocação', '1');
$pdf->AddText('Seção Alocado', '525, 541');
$pdf->AddText('Regional', 'SEIS DE AGOSTO');
$pdf->AddText('Endereço', 'RUA SERTANEJA N. 1777 - CIDADE NOVA');

// Dicas e Procedimentos
$dicas = "Horário de chegada, emissão da zerésima, emissão do boletim de urna (BU), "
       . "Relação com mesários, Pessoas de referência, Pausa para refeição, "
       . "Procedimentos de acesso ao suporte de água e refeição, "
       . "Procedimento para acessar a central de apoio ao fiscal.";
$pdf->AddBlock('Dicas e Procedimentos', $dicas);

// Contatos
$pdf->AddSectionTitle('Contatos');
$pdf->AddText('Nome', 'Gerson Correia Lima Neto');
$pdf->AddText('Cargo', 'Coordenador Regional');
$pdf->AddText('Whatsapp', '(68) 9 9202-5355');
$pdf->AddText('Nome', 'Ana Paula Xavier da Silva Vasconcelos Ferreira ');
$pdf->AddText('Cargo', 'Supervisor local de votação');
$pdf->AddText('Whatsapp', '(68) 9 9237-4420');
$pdf->AddText('Central de Apoio ao Fiscal', 'Contato: (68) 9 9202-5355');

// Gerar o PDF
$pdf->Output();
?>
