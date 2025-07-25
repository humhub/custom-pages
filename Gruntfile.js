module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        cssmin: {
            target: {
                files: {
                    'resources/css/humhub.custom_pages.min.css': 'resources/css/humhub.custom_pages.css',
                    'modules/template/resources/css/humhub.custom_pages.template.min.css': 'modules/template/resources/css/humhub.custom_pages.template.css'
                }
            }
        },
        sass: {
            options: {
                implementation: require('sass')
            },
            dev: {
                files: {
                    'resources/css/humhub.custom_pages.css': 'resources/css/humhub.custom_pages.scss',
                    'modules/template/resources/css/humhub.custom_pages.template.css': 'modules/template/resources/css/humhub.custom_pages.template.scss'
                }
            }
        },
        watch: {
            scripts: {
                files: ['resources/css/*.scss'],
                tasks: ['build'],
                options: {
                    spawn: false,
                },
            },
        }
    });

    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('build', ['sass', 'cssmin']);
};
