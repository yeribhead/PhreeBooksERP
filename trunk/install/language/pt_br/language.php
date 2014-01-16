<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright(c) 2008-2013 PhreeSoft, LLC (www.PhreeSoft.com)       |
// +-----------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or   |
// | modify it under the terms of the GNU General Public License as  |
// | published by the Free Software Foundation, either version 3 of  |
// | the License, or any later version.                              |
// |                                                                 |
// | This program is distributed in the hope that it will be useful, |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of  |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   |
// | GNU General Public License for more details.                    |
// +-----------------------------------------------------------------+
//  Path: /modules/install/language/pt_br/language.php
//

define('LANGUAGE','Portugu�s (BR)');
define('HTML_PARAMS','lang="pt-BR" xml:lang="pt-BR"');
define('CHARSET', 'UTF-8');
// template_welcome
define('LANGUAGE_TEXT','Idiomas Dispon�veis para Instala��o: ');
define('TITLE_WELCOME','Bemvindo - PhreeBooks Contabilidade');
define('TEXT_AGREE','Concordo');
define('TEXT_DISAGREE','Discordo');
define('INTRO_WELCOME', '<h2>Bemvindo ao PhreeBooks Contabilidade</h2>
<p>Este script vai auxili�-lo na instala��o da aplica��o e verificar se seu sistema atende aos requisitos m�nimos. Voc� vai precisar das informa��es a seguir para continuar:</p>
<ul>
  <li>Uma tabela mysql existente com informa��o de acesso</li>
  <li>Direito de escrita no servidor Web (777) para as pastas: /includes e /my_files</li>
  <li>Um nome de usu�rio, endere�o de email e senha para o administrador</li>
  <li>Informa��o de caminho de servidor SSL (isto � recomendado e pode ser modificado se necess�rio)</li>
  <li>O m�s e ano fiscal parea in�cio de armazenamento de lan�amentos no di�rio</li>
</ul>
<p>Por favor, confirme sua aceita��o dos termos de licen�a e tecle Continuar.</p>');
define('DESC_AGREE', 'Eu li e concordo com os termos de Licen�a como descritos acima.');
define('DESC_DISAGREE', 'Eu li e n�o concordo com os termos de Licen�a como descritos acima.');
// template_inspect
define('TITLE_INSPECT','Verificar - PhreeBooks Contabilidade');
define('MSG_INSPECT_ERRORS','Os seguintes erros de instala��o foram encontrados.
<ul>
  <li>Erros (em vermelho) devem ser corrigidos antes que o script de instala��o possa continuar.</li>
  <li>Avisos (em amarelo) n�o impedir�o a instala��o mas podem impedir a opera��o apropriada de alguns m�dulos.</li>
</ul>');
define('INSTALL_ERROR_PHP_VERSION','Sua vers�o de php deve ser 5.2 ou posterior.');
define('INSTALL_ERROR_REGISER_GLOBALS','Registro globais deve ser desabilitado.');
define('INSTALL_ERROR_SAFE_MODE','Sua configura��o php est� habilitada para operar em modo seguro. Modo seguro deve ser desabilitado para instalar o PhreeBooks.');
define('INSTALL_ERROR_SESSION_SUPPORT','Sua configura��o php n�o tem o suporte de sess�o instalao. Suporte de sess�o � necess�rio para executar o PhreeBooks.');
define('INSTALL_ERROR_OPENSSL','Seu servidor deve ter openssl instalado.');
define('INSTALL_ERROR_CURL','Seu servidor de aplica��o php foi instalado sem suporte a cURL. Suporte a cURL � necess�rio para comunica��o segura no envio/recebimento de informa��o para aplica��es remotas.');
define('INSTALL_ERROR_UPLOADS','Seu servidor de aplica��o php foi instalado sem suporte a upload de arquivos. Suporte a upload � necess�rio para importar arquivos para o PhreeBooks.');
define('INSTALL_ERROR_UPLOAD_DIR','N�o foi poss�vel encontrar uma pasta para upload tempor�rio de arquivos neste servidor.');
define('INSTALL_ERROR_XML','Seu servidor de aplica��o php foi instalado sem suporte a XML. Alguns m�dulos podem n�o operar se este suporte n�o estiver dispon�vel.');
define('INSTALL_ERROR_FTP','Seu servidor de aplica��o php foi instalado sem suporte a FTP. Alguns m�dulos podem n�o operar se este suporte n�o estiver dispon�vel.');
define('INSTALL_ERROR_INCLUDES_DIR','A pasta /includes n�o est� acess�vel para escrita. O instalador precisa de acesso a esta pasta para criar o arquivo de configura��o.');
define('INSTALL_ERROR_MY_FILES_DIR','A pasta /my_files n�o est� acess�vel para escrita. O instalador precisa de acesso a esta pasta para armazenar os arquivos de sua empresa.');
define('TEXT_RECHECK','Re-verificar');
define('TEXT_INSTALL','Instalar');
// template_install
define('TITLE_INSTALL','Instalar - PhreeBooks Contabilidade');
define('MSG_INSTALL_INTRO','Por favor entre alguma informa��o sobre sua empresa, administrador, servidor web e banco de dados.');
define('TEXT_COMPANY_INFO','Informa��o Empresa');
define('TEXT_ADMIN_INFO','Informa��o Administrador');
define('TEXT_SRVR_INFO','Informa��o Servidor');
define('TEXT_DB_INFO','Informa��o Base de Dados');
define('TEXT_FISCAL_INFO','Informa��o Fiscal');
define('TEXT_COMPANY_NAME','Entre um nome curto para sua empresa. Ele ser� mostrado no menu pulldown para acesso ao sistema.');
define('TEXT_INSTALL_DEMO_DATA','Voc� quer instalar dados demo para cada m�dulo se disponpivel? ATEN��O: Se optar por sim, os arquivos ser�o limpos antes que os dados demo sejam escritos.');
define('TEXT_USER_NAME','Entre um nome de usu�rio para o administrador');
define('TEXT_USER_PASSWORD','Entre uma senha para o administrador do site');
define('TEXT_UER_PW_CONFIRM','Digite novamente a senha para o administrador do site');
define('TEXT_USER_EMAIL','Entre um endere�o de email para o administrador do site');
define('TEXT_HTTP_SRVR','Entre a URL http do servidor para a pasta raiz (normalmente o valor padr�o � suficiente)');
define('TEXT_USE_SSL','Utilizar SSL para acessar sua empresa (Aten��o: um certificado  SSL deve ser instalado no servidor). Isto pode ser modificado posteriormente se SSL n�o for encess�rio neste momento.');
define('TEXT_HTTPS_SRVR','Entre a URL https do servidor para a pasta raiz para transa��es seguras (normalmente o valor padr�o � suficiente)');
define('TEXT_DB_HOST','Entre o nome da base de dados no servidor');
define('TEXT_DB_NAME','Entre o nome da base de dados (a base de dados deve existir no servidor)');
define('TEXT_DB_PREFIX','Se esta base de dados � compartilhada com outra aplica��o, entre um prefixo para utilizar nesta instala��o (utilize somente caracteres alfab�ticos e underscore):');
define('TEXT_DB_USER','Entre o nome de usu�rio da base de dados');
define('TEXT_DB_PASSWORD','Entre a senha da base de dados');
define('TEXT_FY_MONTH_INFO', 'Selecione um m�s de in�cio para seu primeiro per�odo cont�bil. PhreeBooks vai inicialmente setar o in�cio no primeiro dia do m�s selecionado como per�odo 1.');
define('TEXT_FY_YEAR_INFO', 'Selecione o ano de in�cio para seu primeiro ano fiscal. O m�s selecionado acima e esta sele��o de ano ser�o a menor data em que lan�amentos de di�rio poder�o ser feitas.');

define('ERROR_TEXT_ADMIN_COMPANY_ISEMPTY','O nome da empresa est� vazio');
define('ERROR_TEXT_ADMIN_USERNAME_ISEMPTY', 'O nome de usu�rio do administrador n�o pode ser vazio');
define('ERROR_TEXT_ADMIN_EMAIL_ISEMPTY', 'O endere�o de email do administrador n�o pode ser vazio');
define('ERROR_TEXT_LOGIN_PASS_ISEMPTY', 'A senha do administrador n�o pode ser vazia');
define('ERROR_TEXT_LOGIN_PASS_NOTEQUAL', 'As duas senhas n�o s�o iguais');
define('ERROR_TEXT_DB_PREFIX_NODOTS','O prefixo de tabelas da base de dados s� pode conter os caracteres a-z e _ (underscore)');
define('ERROR_TEXT_DB_HOST_ISEMPTY', 'O nome do host da base de dados est� vazio');
define('ERROR_TEXT_DB_NAME_ISEMPTY', 'O nome da base de dados est� vazio');
define('ERROR_TEXT_DB_USERNAME_ISEMPTY', 'O nome de usu�rio da base de dados est� vazio');
define('ERROR_TEXT_DB_PASSWORD_ISEMPTY', 'A senha da base de dados n�o pode estar vazia');
define('MSG_ERROR_MODULE_INSTALL','Houve erros na instala��o do m�dulo: %s. Veja as mensagens acima para mais detalhes.');
define('MSG_ERROR_CANNOT_CONNECT_DB','N�o foi poss�vel conectar com a base de dados, por favor verifique as especifica��es. Erro retornado: ');
define('MSG_ERROR_INNODB_NOT_ENABLED','A m�quina MYSQL InnoDB n�o est� instalada!. PhreeBooks exige a capacidade de transa��o da m�quina MySQL InnoDB para operar apropriadamente.');
define('MSG_ERROR_CONFIGURE_EXISTS','O arquivo includes/configure.php j� existe, isto pode indicar que o PhreeBooks j� foi instalado ou est� sendo reinstalado. Este arquivo deve ser removido para que a instala��o seja completada com sucesso!');
// template_finish
define('TITLE_FINISH','Fim - PhreeBooks&trade; Contabilidade');
define('TEXT_GO_TO_COMPANY','V� para sua Empresa');
define('INTRO_FINISHED','<h2>Parab�ns!</h2>
<h3>Voc� instalou com sucesso PhreeBooks&trade; Contabilidade em seu sistema!</h3>
<h2>Pr�ximos Passos</h2>
<p>Uma lista de Coisas a Fazer foi gerada identificando as a��es necess�rias para operar os m�dulos instalados. Esta lista vai aparecer no painel da p�gina principal do administrador. Configura��o adicional de mp�dulos, prefer�ncias e especifica��es podem ser feitas atrav�s do M�dulo Empresa Menu Admin.</p>
<p>Por raz�es de seguran�a, voc� deve alterar as permiss�es para seu arquivo de configura��o para somente leitura. Este arquivo pode ser encontrado na pasta <strong>/includes/configure.php</strong>. Se voc� habilitou acesso total a pasta includes para instala��o, isto pode ser modificado para a especifica��o anterior. Tamb�m � necess�rio que a pasta <strong>/install</strong> seja removida ou renomeada para prevenir a reinstala��o da aplica��o.</p>
<h2>Documenta��o e suporte</h2>
<p>PhreeBooks inclui um sistema de aux�lio sens�vel a contexto. Como acontece com a maioria das aplica��es de c�digo aberto, este � um trabalho em processo cont�nuo mas prov� uma orienta��o e suporte gerais.</p>
<p>Existe tamb�m uma  documenta��o online no site PhreeBooks (<a target="_blank" href="http://www.phreebooks.com">www.PhreeBooks.com</a>). O modulo de documenta��o mais atual est� disponpivel bem como outras FAQs e suporte a customiza��o/desenvolvimento.</p>
<p>Finalmente, o f�rum de usu�rios localizado no site PhreeBooks prov� suporte comunit�rio e permite postar d�vidas, problemas e sugest�es. Se voc� estiver travado, sinta-se a vontade para postar uma d�vida! N�s temos uma comunidade ativa, amig�vel e com conhecimento que vai receb�-lo muito bem.</p>');

?>