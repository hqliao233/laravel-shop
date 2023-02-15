<?php
return [
    'alipay' => [
        'app_id'         => '2016101000652342',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5LYBh3DT+fM+lCzIseRgEKXfPryEQzYM272YVpf+fOmshzgr8uM1Zo/wnYF3/JhvQbAqzfZQ0Ci0svxkmIo+9SjxAdWwhsNjByxtwCt0poTjw24RpzQcAlrDOe0GsiDeDUE86IS+SY4GXUXA4diX0du2ARZXdOlG7dEMGzSMerbUUBJrrdaLzPXc6rH4LENCZD2dpYkZXAN1Sdo00vuSyCa+5PwV+zdoUFaLg6JJMkldtBfyfVCOD15F0OgMTaJXGcBAARVYGc3nCCh2f9/V657PkUa/7c4CQqCTd3e7FnuTOuXN5I7nt+3w45ldWyUHT6hVUs+oVgg47L18pQcCjQIDAQAB',
        'private_key'    => 'MIIEowIBAAKCAQEAmxjAFT0/qGqLxEGm9wQYg6FOGqYkDQ9KLDjr2d+FPz1s/JYj7H2bQl9BazRAOkld3vX/1xQBIOUtGy2zUUtlDZcxHcxqJzc9w4PsWfo1+cm1kQa0LdVqpEp3mOr3Z6bZUfii0eNDb7vz3rJBl1xNsz7B3voX7KhSUj+m8LnJ0UFss2MCm1X39ahZrnduJd74F73gXny+y+h/CsdD8t56/efL9E6YWNeCCNwSABF1cLEm0/lRg9rBob+6V6ZgQskj+y7r5zGkw32hU0dElQKcQzPRNHYs53i61/T7GNKai1ThbrrIXhrwpna4CBdeBCpHcOiToxxeMyHDcJ2I//4bHQIDAQABAoIBAE12QxlYid63uC7BC3770LKNcdDlgcP0CvujQkiC1q4LI8/TvQV0hoLTyHyekCWMVxHwk2L8XsPrMmgMny7PFR2AkctsOOh1Mqffz7/ws9yZ3dEdJAhwOt7rQmhhkkd2kbvbDpb9hsTbfqWPKitKspP/mByhlCliuBrUJ7C7WgzkdTbb0NMOPHIgXN2hhjl6OQ8JumhmoCh5DHKzwIPzreXjUJhceEtBvNDmQH91Iq3DTZavYBm3BLWt+eQAZwjf42DMJHS7J1T1svl2Gyv+paOVVu8DuCSwMg4qceVoaEUQldBFA3YUx6iByZHDZIJ/k3mcmh2uM3ipPoN+E8VlIRECgYEAyBe/u5W/yAqJ/0oczr+kqohEXLB85lAtghAL7ANdANPcgbCX3BVruT7kZqkPd+pu+lU0brEPlMqbUWzBHCDDjhe/4WCm//lEgkW6/D50tPFLF9pb/x/ohXQXh56ifJ5z5AFGTz9X4zPy84oodOJhwS6YSbQy3/WrXa+NqQ8cJWcCgYEAxm6EhwHXAyXNPZhEcXNNFcVDEXmfDCQP6wYuKcse2roRXwIyLavdGIMdmzcNvI4wcM4BPjpSDe0NMz/Ykn2BeDm4RJ0hxyuqdw/6zif9K1iUpKuTyaqXYeuqpRBtw+ieFNpVcO5E7n88dEhF5Qzad1u/SbJHznZM87+GQrW3hNsCgYEAxj7hdFW2IdhvSUSArkcqOtuv/7XMydP+grKrXTcn2j4OZ2Az1ZZTmMI7QYuXC9R2CAiLXnCHY4Apys7ZtaiwmAPn1MESNOMfgYLc9DZdIzk+kW+DXo6arj6LYY9GpfnIEMtCghncVfuOZsUnuEbjW6dysvOaxFJ/at8Yl7ukw1kCgYB16TApBUfdn/XQdw2cmJYirKAI30tKLJek4XxG62L/N/egjC6DAxATQ74xAErSU2+naFJGxuMBUW45mOO5UIjBhxLe+rwanPDjZQR0wkBnJHDXVPkxqYPg9Ofmw9dlxXFCFOcWfw9I5SRoOJYH4FZk/IizYxPWNGfPo2xFcbOGZwKBgBTYo8UpJioS2toUqcHnnh/bqbFpjAIudArrHuDnC2o+MTpR+f84wJfiUYzeF/cCYpg8nQAjD5NMR35pWc74BCNezmssXpd0arMY2ZgCxaTv9P/qdDpJ9tWqi+MlGaws60GzGOe8KVRIWZYobRHH9+inhPTapT8jOU6PrC1McNdN',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],
    'wechat' => [
        'app_id'      => '',
        'mach_id'     => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
