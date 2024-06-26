<div style='font-family:"DejaVu Sans","sans-serif";'>
<p align="center">
    <strong>ПИСЬМО-ОФЕРТА</strong>
</p>
<p>
Настоящим сообщением изъявляю безотзывную волю приобрести <strong>Товар</strong>, программно выбранное и обозначенное мной на
    платформе, и имеющее соответствующие характеристики, которым(и)
    является/являются:
</p>
    @php
        function getSeasonByOffer(string $date)
        {
            $month = Carbon\Carbon::parse($date)->month;
            switch ($month) {
                case 12:
                case 1:
                case 2:
                    return 'Зима';
                case 3:
                case 4:
                case 5:
                    return 'Весна';
                case 6:
                case 7:
                case 8:
                    return 'Лето';
                case 9:
                case 10:
                case 11:
                    return 'ошибка';
            }
        }
    @endphp
    @foreach($trees as $tree)
        <p>
            {{ $loop->iteration }}.
            Товар - {{ $tree['tree_type'] }};
            ID номер - {{ $tree['uuid'] }};
            год посадки - {{ date('Y', strtotime($tree['planting_date']))  }},
            сезон - {{ getSeasonByOffer($tree['planting_date'])}};
            цена - {{
        $tree['purchase_price']
            ? number_format($tree['purchase_price'] / 100, 0)
            : number_format($tree['current_price'] / 100, 0)

    }} долларов США;
            локация - {{$tree['location'] }};
            номер поля - {{ $tree['cadastral_number'] }};
            кооператив - {{ $tree['cooperative_name'] }};
            координаты - {{ json_decode($tree['coordinates'],true)['lat'] }}, {{ json_decode($tree['coordinates'],true)['lng'] }}
        </p>
    @endforeach
<p>
    Допуск к платежной транзакции для приобретения вышеуказанного Товара и
    указанным фактическое осуществление/возможность расчета рассматриваю
    ответным акцептом моего предложения и соответственно, в связи с показанным
    вопросом, заключение сделки купли-продажи, обязательной для выполнения и
    имеющей обязывающий характер, на условиях, предварительно заявленных в
    характеристике приобретаемого имущества.
</p>
<p>
    Подтверждаю, что выраженная мной в настоящем сообщении заинтересованность
    основывается на детальном изучении, ознакомлении с размещенными на
    платформе характеристиками и данными приобретаемого имущества и полноценном
    владении информации для принятия решения, что в дальнейшем не создаст
    причину или основание для возникновения отличных ожиданий и что само собой
    исключает возникновение каких-либо претензионных вопросов с этой стороны.
</p>
<p>
    Дополнительно заранее изъявляю волю, в случае акцептирования настоящего
    сообщения и приобретения Товара, автоматически включиться в ассоциированные
    члены  <strong>Кооператива</strong> Family olive club Georgia на период владения Товаром
    и на условиях, с которыми я полностью ознакомлен и настоящим сообщением
    подтверждаю их.
</p>
<p>
    Заявляю о согласии, чтобы на это сообщение и на приобретение с его помощью
    Товара распространялось и действовало только законодательство и судебная
    юрисдикция государства Грузии, что исключает распространение права и
    судебной компетенции другой страны на преддоговорные и договорные условия и
    процедуру.
</p>
<p>
    Подтверждаю, что личные данные, платежные инструменты и намерение,
    заявленные мной в этом сообщении и на стадии его акцептирования,
    являются/будут являться достоверными/действительными и отображают законное
    доверие, опираясь на которые, ответное действие или ожидание, не могут быть
    признаны подозрительными.
</p>
<p>
    Заявляю о согласии, чтобы в случае подтверждения этого сообщения, при
    помощи платформы и активированного на ней электронного кошелька,
    привязанных платежных инструментов, без дополнительного акцептирования, с
    моего счета было осуществлено снятие сумм, равносильных цене купли-продажи
    для приобретения заявленного Товара.
</p>
<p>
    Заявляю о согласии, чтобы приобретение заявленного Товара было признано
    завершенным, и была признана моя собственность на них только после того,
    как с моей стороны в полном размере будет уплачена рассчитанная цена.
    Вместе с тем, заявляю о согласии на то, чтобы неполная уплата не подлежала
    возврату, если в течение 3(трех) календарных дней с моей стороны до конца
    не будет выполнено возложенное обязательство, и после указанного срока
    настоящее сообщение и установленные на его основании отношения были бы
    признаны аннулированными/прекращенными.
</p>
<p>
    Настоящее сообщение вступает в силу незамедлительно, отзыву,
    изменению/модификации, в том числе отказу от него не подлежит и порождает
    заявленные юридические последствия для автора этого сообщения и его
    адресата без их дальнейшего пересмотра и отказа от них.
</p>
<p>
    Заявляю о согласии на то, чтобы оформление настоящего сообщения и
    установленных на его основании отношений было засчитано и приравнено к
    письменной форме.
</p>
</div>
