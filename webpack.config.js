const path = require('path');

module.exports = [
  {
    watch: true,
    entry: {
      'filer': './src/Modules/Upload/Controllers/_resources/filer.js'
    },
    module: {
      rules: [
        {
          test: /\.css$/,
          use: ['style-loader', 'css-loader'],
        },
      ]
    },
    output: {
      libraryTarget: "global",
      path: path.resolve(__dirname, 'src/Modules/Upload/Controllers/_resources/'), //directory for output files
      filename: '[name].min.js' //using [name] will create a bundle with same file name as source
    },
  }
];
