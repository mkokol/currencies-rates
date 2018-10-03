module.exports = {
    outputDir: 'public',
    assetsDir: 'assets',
    indexPath: '../templates/index.html',
    configureWebpack: {
        entry: {
            app: './src/assets/main.js'
        }
    }
}
