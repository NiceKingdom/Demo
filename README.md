# 繁盛王国

Demo 版本：0.1 ver。

# 部署

```bash
# Backend
# fork and goto product
# copy file .env.example to .env
# write .env, completar APP_KEY & DB_* & REDIS_* & MAIL_*
run `php artisan key:generate`
run `php artisan migrate --seed`
run `php artisan serve`

# Frontend
# install dependencies
npm install

# copy index.js.bac to index.js, update your config/index.js::proxyTable.host
# on cros, the test server to accept port is 8080 or 8081
#     proxyTable: {
#           '/': {
#             target: 'http://www.nice-kingdom.uio',
#             changeOrigin: true,
#           }
#         },

# serve with hot reload at localhost:8080 or other tips
npm run dev

# build for production with minification
# npm run build

# build for production and view the bundle analyzer report
# npm run build --report

# Test
# run `./vendor/bin/phpunit [testFile]`, eg. `tests/Feature/ResourcePolicyTest.php`
```

## 开发

如果您想为本项目提供 PR，请遵循 [开发风格](0Doc/work-style.md)。
（前端额外使用 ESLint 检查器）

# 许可（LICENSE）

本项目（NiceKingdom 游戏的 Demo）遵循 BSD 开源许可证。
NiceKingdom-Demo is BSD licensed.

