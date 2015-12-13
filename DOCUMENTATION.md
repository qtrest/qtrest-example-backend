Привет.

В данном файле описывается архитектура сервиса в общем виде.

Итак, предназначение сервиса - аггрегировать инфомрацию о скидках в Казахстане и в перспективе во всём СНГ.
Зачем это нужно? Чтобы было проще искать интересные скидки конечно!

Что же представляет собой сервис изнутри. Это приложение, разработанное с использованием Yii2/PHP/MySql и SleepingOwl/Apist.

Приложение имеет две точки входа - пользовательскую и техническую.

Ползовательская точка входа представлена методам контроллера CouponController. Она позволяет пользователям просматривать актуальный и архивный контент из базы данных, а также обращаться к тектсовым разделам сайта.

В общем всё довольно стандартно и скучно.

Интерес представляет техническая часть проекта. Вся логика данной части расположена в модуле kupon. Логически он делится на 2 части:
1. Контроллер;
2. Набор парсеров.

Контроллер предсотавляет доступ к методам запуска каждого из парсеров, а также методы для визуального тестирования работы парсеров.
На данный момент контроллер предоставляет следующие методы:
- actionFetchall - проинициализировать и получить новые купоны со всех сервисов;
- actionUpdateall - обновить несколько случайных записей из разных сервисов - проверить их актуальность и получить расширенную инфомрацию со страницы каждог окупоны;
- actionTestapi - позволяет протестировать получение различных групп данных для каждого из сервисов.

Основной функционал разумеется содержится в наборе парсеров. Набор парсеров реализовать таким образом, что в системе всегда присутствует один базовый класс, который занимается непосредственной реализацией 90% бизнес логики, а также несколько классов-потомков, которые занимаются получением и адаптацией данных с каждог осервиса под один формат.

Базовым классом, от которого наследуются все остальные классы является BaseApi. Он реализует публичный инфтерфейс для получения данных,а также набор абстрактынх методов, которые необходимо реальзовать в каждом новом классе-потомке.

Минимальный набор абстрактных методов для переопределения в каждом потомке следующий:
- getBaseUrl - базовый адрес купонатора;
- getSourceServiceCode - уникальное кодовое обозначение сервиса;
- getSourceServiceName - унакальное наименование сервиса, которое будет отображаться в пользовательском интерфейсе;
- getCountryName - наименование страны на русском языке с боольшой буквы, в которой представлен данный сервис. Предполагается, что один сервис представляет всегда лишь одну страну;
- getCountryCode - уникальное кодовое обозначение страны, в которой представлен сервис;
- cities - метод, который формирует массив данных о городах, которые доступны в данном сервисе;
- categories - 