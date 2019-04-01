module.exports = function (grunt)
{
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-copy');

  grunt.initConfig(
    {
      clean: ['src/Base/Components/CkEditor/_resources/widget/'],
      copy: {
        main: {
          files: [
            // includes files within path
            {
              expand: true,
              cwd: 'node_modules/@packaged-ui/ckwidgets/build/',
              src: '**',
              dest: 'src/Base/Components/CkEditor/_resources/widget/'
            }
          ],
        },
      },
    }
  );

  grunt.registerTask('default', ['clean', 'copy']);
};
