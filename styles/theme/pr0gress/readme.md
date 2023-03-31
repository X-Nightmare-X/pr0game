# pr0gress theme

icons by [Freepik](https://www.flaticon.com/search?author_id=1&style_id=162&type=standard&word=satellite)

planets by Timo_KA, Slippy, DawnofUwe, d0xxy
ships, research, buildings by steamnova (to be replaced in later release)

css style by d0xxy


## convert images

```sh
# execute in directory containing pngs
mogrify -resize '200x200^' -gravity Center -extent 200x200 -format jpg -quality 99 *.png
```
