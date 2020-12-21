## Release
[![GitHub Release](https://img.shields.io/github/release/tterb/PlayMusic.svg?style=flat)]() 

### Pré Requisitos

- Magento 1.x

---

#### Fluxo resumido:

- O **cliente (loja)** faz uma parceria comercial com a Clique Retire
- A Clique Retire fornecerá as credenciais que deverão ser adicionadas no painel administrativo da loja em **Configuration > Shipping Methods > Clique Retire** e também fornecerá um login e senha para acesso a plataforma **Alfred**
- O usuário comprador, na loja virtual do cliente, consulta o tempo e valor do frete **via api** ao escolher a Clique Retire como transportadora que oferece a opção de retirada em um dos **e-Box (locker)** da rede Clique Retire
- No momento do **pick and packing** a loja através da plataforma **Alfred** deverá imprimir a etiqueta de transporte (WB) e solicitar a coleta da(s) remessa(s)
- O courier da Clique Retire realizará a coleta no **centro de distribuição do cliente (loja)** e levará até o CrossDocking da Clique Retire para triagem
- A Clique Retire realiza a entrega do pedido fisicamente no e-Box escolhido pelo comprador
- O comprador receberá um **SMS e E-mail** com um **QrCode/pincode** para retirar o pedido
- O comprador vai até o local e faz a retirada do pedido

#### Plugin Clique Retire

1. Na raiz do projeto, compacte a pasta `app` como **plugin.zip**, ou faça o download do arquivo [aqui](plugin.zip)
2. Faça o upload do arquivo **plugin.zip** para a pasta `/tmp` do servidor da loja magento
3. Descompacte o arquivo `plugin.zip` e mova as pastas para `/var/www/html/magento`

```
unzip plugin.zip -d plugin/
sudo cp -R /tmp/plugin/app/* /var/www/html/magento/app/.
```

4. Realize a limpeza das pastas de Cache e neinicie o serviço web
Exemplo:
```
sudo rm -rf /var/www/html/magento/var/cache
sudo rm -rf /var/www/html/magento/var/session
sudo systemctl restart apache2
```
