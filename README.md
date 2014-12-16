Calendário com fotos do Mapillary
========

#Sobre
Crie seu próprio calendário de 2015 com fotos do Mapillary

É um gerador de calendário que utiliza fotos do [Mapillary](http://www.mapillary.com/), feito em php que utiliza a biblioteca [phpqrcode](http://sourceforge.net/p/phpqrcode/wiki/Home/).

Visite o site [https://projetorgm.com.br/calendario/](https://projetorgm.com.br/calendario/) e confira o resultado.

Para utilizar, basta baixar o fonte e configurar os dados do banco de dados MySQL (para registrar os calendários gerados).

Compartilhado na esperança de ser útil. Use por sua conta e risco. Divirta-se!

#About
Create your own 2015 calendar with Mapillary photos

It is a calendar generator that uses [Mapillary] (http://www.mapillary.com/) photos, made in PHP that uses the library [phpqrcode] (http://sourceforge.net/p/phpqrcode/wiki/ Home /).

Visit the [https://projetorgm.com.br/calendario/](https://projetorgm.com.br/calendario/) website and check the result.

To use, just download the source and configure the data of the MySQL database (to record the calendars generated).
Create your own calendar with Mapillary photos
Shared on hoping to be useful. Use at your own risk. Enjoy!

##Estrutura do banco de dados
As tabelas devem ter os seguintes campos:

Tabela Calendars
"ID";"Date";"Time";"SourceKeys";"Type";"Country"

Tabela Stats
"AutoID";"Date";"Time";"Calendar"

##LICENSING

Edil Queiroz de Araujo - Projeto RGM 

This library is free software; you can redistribute it and/or modify it under
the terms of the GNU Lesser General Public License as published by the Free
Software Foundation; either version 3 of the License, or any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU Lesser General Public License (LICENSE file)
for more details.

You should have received a copy of the GNU Lesser General Public License along
with this library; if not, write to the Free Software Foundation, Inc., 51
Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
