# Projeto Smartchurch

Sistema web (RESTFul) voltado para gestão de igrejas presbiterianas

### Requerimentos de Software

* Nginx
* PHP7+ (gd, xml, zip, curl, gmp, imagick, json, bz2, mbstring, mcrypt, xmlrpc)
* Banco SQL (testado com MySQL 8+, mas deve funcionar com qualquer banco SQL)
* Conta de e-mail para envio de notificações

## Organização das pastas

/api -> contém o backend (PHP + SQL)

/app -> contém código (legacy) do app Android

/inscricoes -> cliente web para usar a API de inscrições em eventos

/mural -> cliente web para usar a API de murais

/painel -> contém o frontend (Angularjs)

/projeto -> contém o modelo do banco de dados e o script sql de criação do banco

/scripts -> scripts diversos de auxílio

/wp-plugin -> plugin wordpress (PHP) para usar a API de inscrições em eventos, sermões e séries de sermão

### Uso

* Clona o repositório inteiro
* Configura o banco de dados com o script de criação em projeto/create.sql
* Importa os dados básicos com o sql em projeto/dados.sql
* Configura o arquivo principal de configurações em api/app/config/conf.cfg.php (use como base o api/app/config/conf.cfg.sample.php):
    - paths do sistema em ambiente de desenvolvimento, testes e produção
    - acesso ao banco de dados
    - senha mestre
    - conta do e-mail de notificação
* Cria o usuário principal (root) diretamente no banco de dados na tabela usuário scom os campos:
    - perfil = 201801311908460356487174
    - stat = ATV
    - tp = STF
    - is_master = N
* Cria o vhost no nginx usando como base o arquivo em nginx/vhost.conf
* Se em ambiente de desenvolvimento, altere o arquivo /etc/hosts para reconhecer o name smartchurch.local. Ex: [opcional]
    127.0.1.1	smartchurch.local
* Rode no navegador