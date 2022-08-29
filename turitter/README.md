# turitter
## アプリの概要
まず初めに、これは釣り好きの人・釣りをやってみたい人のために作った釣った魚の位置情報を共有するサイトです。やっぱり魚が釣れないと釣りは面白くありません。そのためにまず何が必要なのかというと、それは情報です。簡単な一例ですが、もしイワシの群れの発見情報があれば、それ以降数日そこにイワシの群れは訪れます。そうなれば、釣り用具さえあれば初心者でもイワシを大量に釣ることができます。これこそが釣りの醍醐味だと思います。
こういった群れの情報を手にいれるには毎日釣りに行ってる人やそういった人とツテのある人しか知り得ることができません。これらを払拭できるのがこのサイトです。皆さんはおそらくネットサーフィンすれば見つけることができるお思いだと思います。しかし、こういった群れの情報というのは突発的なものでなかなかサイトに載せられていないものです。また、詳しい位置までをなかなか把握することもできません。ですから当サイトでは、投稿してくれた写真からGPSを読み取り、Google Maps で位置情報を特定できるようになっています。また、取った(釣った)時間帯も手に入れる事もできるようにしているため、いつどこでどんな魚が何時に釣れたのかを把握することができます。そして、誰でも投稿できるため、皆さんに最新情報を得られることができると思います。他の機能として、掲示板の投稿内容から特定の情報だけ絞る事ができるように検索機能をつけました。自分が釣りたい魚の情報・自分が行く釣り場で何が釣れているのかを検索することが出来ます。この当サイトを利用することで、釣りの楽しさを伝えることができればと思います。

## 使用言語
HTML/CSS/JavaScript/PHP/MySQLで構成されています。



## 機能一覧
- 検索機能
- 削除機能
- 編集機能
- 写真をアップロードする機能
- 写真の位置情報と日時の取得機能
- 自動的にログインする機能
- 自動的にログアウトする機能
- ユーザー情報を保存する機能

## 工夫点や注力した機能
工夫点として、ログインせずに当サイトを利用できないように、session関数を用いて不正侵入を防ぎました。そして、1時間放置した場合、自動的ににログアウトするようにしました。
また、魚の写真の位置情報を取得して、Google mapsに表し、位置情報の共有を可能にし、撮影日も取得することでいつの時期の何時に釣れたのかを把握できるようにしました。
注力した機能は、やはり写真の位置情報と日時を取得して、Google maps に表すことのできる機能です。
他にも、メッセージ内容にも基づいて、検索できるようにした検索機能も注力しました。

## 補足
拡張子がHEICの場合、うまくGoogle Mapsに反映されません。誠に申し訳ございません。

## デモ画面
https://youtu.be/JJuf6-OlvKg

## テストアカウント
- サイト: https://tb-220337.tech-base.net/turitter/index.php
- メールアドレス: guest1234@gmail.com
- パスワード: pass
- 現在APIを有効にはしていません。

## 注意点
写真のGPSを用いてGoogle mapsで位置情報を共有するため、個人情報の取り扱いにはご注意ください。くれぐれも家で取った写真を送らないでください。削除すれば、写真ごと消すため間違って送ってしまったら、できるだけお早めにお消しください。

## Application Overview
First of all, this is a site to share the location information of the fish you caught, created for people who love fishing and want to try fishing. After all, fishing is not fun unless you can catch fish. What is needed first for that is information. For example, if a school of sardines is found, they will visit the area for a few days. If that happens, even a beginner with fishing gear can catch a lot of sardines. This is the best part of fishing. To get information about such schools, only those who go fishing every day or have contacts with such people can get it. This site is the place to get rid of this problem. You probably think that you can find them by surfing the net. However, information on schools like this is often unexpected and not easily found on websites. It is also difficult to get a detailed location of the schools. Therefore, this site can read the GPS from the photos you submit and identify the location using Google Maps. We are also able to obtain the time of day when the fish was caught, so we can determine where and what kind of fish was caught and at what time. And since anyone can post, I think everyone can get the most up to date information. Another feature is the search function, which allows you to narrow down your search to specific information. You can search for information on the fish you want to catch and what is being caught at your favorite fishing spots. By using this site, we hope to convey the joy of fishing.

## Languages used
HTML/CSS/JavaScript/PHP/MySQL
