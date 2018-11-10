# nice-kingdom

> Game

## Build Setup

``` bash
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
npm run build

# build for production and view the bundle analyzer report
npm run build --report
```

For a detailed explanation on how things work, check out the [guide](http://vuejs-templates.github.io/webpack/) and [docs for vue-loader](http://vuejs.github.io/vue-loader).
