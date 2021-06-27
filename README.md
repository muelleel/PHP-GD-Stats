# PHP-GD-Stats
Some more or less 'ugly' chart generators written in PHP using the GD-Libary

## How to
You can simply call the Scripts in an HTML-Image tag:

```
<img src="path/to/script/chart.php?data=[1,8,3,0,2,3,0,2,1,1,3,1]" />
```

be sure to urlencode the data-field properly, like in the Examples!

## Examples
### curve-chart

simple json list of numbers:
```
[1,8,3,0,2,3,0,2,1,1,3,1]
```

urlencoded:
```
%5B1%2C8%2C3%2C0%2C2%2C3%2C0%2C2%2C1%2C1%2C3%2C1%5D
```

### bar-chart

key-value json array

```
{"Some Name": "1", "Someone": "2"}
```

urlencoded:
```
%7B%22Some%20Name%22%3A%20%221%22%2C%20%22Someone%22%3A%20%222%22%7D
```

### pie-chart

key-value json array
```
{"Some datafield": "1", "datafield": "2"}
```

urlencoded:
```
%7B%22Some%20datafield%22%3A%20%221%22%2C%20%22datafield%22%3A%20%222%22%7D
```

## Info

I wrote theese chars some years ago, maybe someone is searching for it. Feel free to use and change the Code to your belongs.