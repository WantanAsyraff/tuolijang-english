"use strict";
const { run } = require('runjs')
const chalk = require('chalk')
const config = require('./vue.config.js')
const fs = require('fs')
const path = require('path')
const rawArgv = process.argv.slice(2)
const args = rawArgv.join(' ')


  const report = rawArgv.includes('--report')

  const port = 3000
  const publicPath = config.publicPath

  var connect = require('connect')
  var serveStatic = require('serve-static')
  const app = connect()
  const distDirectory = path.join(__dirname, 'dist')

  app.use(
    publicPath,
    serveStatic(distDirectory, {
      index: ['index.html', '/']
    })
  )

  // Vue Router owns routes below the dashboard base path. Serve the app shell
  // when a browser opens or refreshes a nested route such as /admin/login.
  app.use(publicPath, (req, res, next) => {
    if (!['GET', 'HEAD'].includes(req.method)) {
      return next()
    }

    const indexFile = path.join(distDirectory, 'index.html')
    res.setHeader('Content-Type', 'text/html; charset=utf-8')
    if (req.method === 'HEAD') {
      return res.end()
    }

    fs.createReadStream(indexFile)
      .on('error', next)
      .pipe(res)
  })

  app.listen(port, function () {
    console.log(chalk.green(`> Preview at  http://localhost:${port}${publicPath}`))
    if (report) {
      console.log(chalk.green(`> Report at  http://localhost:${port}${publicPath}report.html`))
    }

  })
