<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011, 2012 PhreeSoft, LLC       |
// | http://www.PhreeSoft.com                                        |
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
//  Path: /modules/phreeform/language/en_us/admin.php
//
// Module information
define('MODULE_PHREEFORM_TITLE','Módulo Relatórios');
define('MODULE_PHREEFORM_DESCRIPTION','O Módulo Relatórios contém todas as ferramentas e formulários para imprimir relatórios em  formato PDF ou HTML. <b>ATENÇÃO: Este é um módulo central e não deve ser removido!</b>');
// title
define('BOX_PHREEFORM_MODULE_ADM', 'PhreeForm Administração');
// Headings and Helpers
define('PB_CONVERT_REPORTS','Converter Relatórios .txt para PhreeForm');
// admin defines
define('PB_CONVERT_SAVE_ERROR','Aconteceu um erro ao salvar o relatório convertido: %s');
define('PB_CONVERT_SUCCESS','Conversão bem sucedida de %s relatórios e formulários. Se houve algum erra durante a conversão, ele deve ter sido mostrado em uma mensagem anterior.');
// Module configuration defaults
define('PF_DEFAULT_COLUMN_WIDTH_TEXT','Especifica a largura padrão para largura de colunas do relatório em mm (padrão: 25)');
define('PF_DEFAULT_MARGIN_TEXT','Especifica a margem de página padrão para uso em relatórios e formulários em mm (padrão: 8)');
define('PF_DEFAULT_TITLE1_TEXT','Especifica o texto de título padrão para imprimir como cabeçalho nos relatórios (padrão: %reportname%)');
define('PF_DEFAULT_TITLE2_TEXT','Especifica o texto de título padrão para imprimir como cabeçalho 2 nos relatórios  (padrão: Relatóreio Gerado em %date%)');
define('PF_DEFAULT_PAPERSIZE_TEXT','Especifica o tamanho de página padrão para uso em relatórios e formulários (padrão: Carta)');
define('PF_DEFAULT_ORIENTATION_TEXT','Especifica a orientação de página padrão para uso em relatórios e formulários (padrão: Retrato)');
define('PF_DEFAULT_TRIM_LENGTH_TEXT','Especifica a abreviaçãode nomes de relatórios e formulários ao listar em formato de diretório (padrão: 25)');
define('PF_DEFAULT_ROWSPACE_TEXT','Especifica a separação entre as linhas de cabeçalho para relatórios (padrão: 2)');
define('PDF_APP_TEXT','Especifica a aplicação geredora de PDF padrão . Atenção: TCPDF é exigido para UTF-8 e geração de código de barras.');
// Tools
define('PHREEFORM_TOOLS_REBUILD_TITLE','PhreeForm Verificação Estrutura / Reconstruir');
define('PHREEFORM_TOOLS_REBUILD_DESC','Este ferramente verifica e reconstrói a estrutura de relatórios e formulários. Vai recarregar a estrutura da pasta, certificar-se de que não há relatórios órfãos e limpar qualquer entrada na tabela que não tenha um relatório/formulário associado a ela');
define('PHREEFORM_TOOLS_REBUILD_SUBMIT','Iniciar Verificação Estrutura / Reconstruir');
define('PHREEFORM_TOOLS_REBUILD_SUCCESS','Reconstrução de tabela de relatórios bem sucedida. O número de relatórios reconstruido foi %s. %s relatórios órfãos foram colocados na pasta Miscellaneous.');
?>