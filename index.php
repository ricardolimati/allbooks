<?php

function writeLog($message) {
  // Define o caminho do arquivo de log
  $logFile = 'logfile.log';

  // Formata a mensagem de log com data e hora
  $formattedMessage = date('Y-m-d H:i:s') . " - " . $message . PHP_EOL;

  // Abre o arquivo de log no modo de adição
  if ($handle = fopen($logFile, 'a')) {
    // Escreve a mensagem formatada no arquivo de log
    fwrite($handle, $formattedMessage);
    // Fecha o arquivo
    fclose($handle);
  } else {
    // Caso não seja possível abrir o arquivo, lança um erro
    echo "Não foi possível abrir o arquivo de log!";
  }
}

function logPostVariables() {
  // Verifica se o método da requisição é POST
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Itera sobre todas as variáveis de $_POST
    foreach ($_POST as $key => $value) {
      // Cria uma mensagem de log com o nome e valor da variável
      $message = "POST variable '$key' has value '$value'";
      // Escreve a mensagem no log
      writeLog($message);
    }
  } else {
    echo "Nenhum dado POST para gravar.";
  }
}

// Chama a função para registrar as variáveis de $_POST
logPostVariables();

$Nome = $_POST['Nome'] ?? '';
$Email = $_POST['Email'] ?? '';
$Telefone = $_POST['Telefone'] ?? '';
$Nome_da_empresa = $_POST['Nome_da_empresa'] ?? '';
$CNPJ = $_POST['CNPJ'] ?? '';
$Produto_de_interesse = $_POST['Produto_de_interesse_(Selecione)'] ?? '';
$Mensagem = $_POST['Mensagem'] ?? '';

// Verifique se todas as variáveis necessárias estão definidas
if ($Nome && $Email && $Telefone && $Nome_da_empresa && $CNPJ && $Produto_de_interesse && $Mensagem) {
  $data = [
    "name" => $Nome,
    "legalName" => $Nome_da_empresa,
    "cnpj" => $CNPJ,
    "description" => $Produto_de_interesse . " | " . $Mensagem . " | " . $CNPJ,
    "address" => [
      "postal_code" => "00000-000",
      "country" => "Brasil",
      "district" => "",
      "state" => "SP",
      "street_name" => "",
      "street_number" => "",
      "city" => ""
    ],
    "customFields" => [
      'Mensagem' => $Mensagem
    ],
    "category" => "V4",
    "ranking" => "3",
    "contact" => [
      "email" => $Email,
      "work" => $Telefone,
      "mobile" => $Telefone,
      "whatsapp" => "+55 00 00000-0000"
    ]

  ];

  $payload = json_encode($data);

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.agendor.com.br/v3/organizations/upsert',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => array(
      'Authorization: Token 77dced53-ae21-40b7-9443-d06300217240',
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);

  // Verifica se houve erro no cURL
  if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
    writeLog("cURL error: " . $error_msg);
  }

  curl_close($curl);

  // Exibir e registrar a resposta da API
  echo "Response: " . $response . "\n";
  writeLog("Response: " . $response);
} else {
  echo "Algumas variáveis POST estão faltando.";
  writeLog("Algumas variáveis POST estão faltando.");
}
