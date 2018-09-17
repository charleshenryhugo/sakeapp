## introduction

This is the backend part of our project for the OSPD summer internship.

Believe or not, it helped us win the first place.

## migrate

`php artisan migrate`

## GET test method

`php artisan serve`

 GET the following urls:
 (make sure you have enough data to your database)

```
http://localhost:8000/api/omakase?postcode=2778888

http://localhost:8000/api/set_order?set_id=1

http://localhost:8000/api/items?postcode=1580094&keyword=sawayaka,kankitsu&strength=low

http://localhost:8000/api/purchase?item_id=1,3,5,7
```