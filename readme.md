# 繁盛王国

Demo 版本：0.1 ver。

# 部署

```ini
# fork and goto product
copy file .env.example to .env
write .env, completar APP_KEY & DB_* & REDIS_* & MAIL_*
run `php artisan migrate --seed`
run `php artisan serve`
# run `npm run watch`
run `sass --watch resources/assets/sass:public/css`
# unit test (non-required)
go to 127.0.0.1:8000
```

## 开发

如果您想为本项目提供 PR，请遵循：
[开发风格](https://github.com/Sun-FreePort/testR/blob/master/0Doc/work-style.md)

# 许可（LICENSE）

本项目遵循 BSD 开源许可证。
testR is BSD licensed.
