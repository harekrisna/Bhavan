module.exports = function(grunt) {
    grunt.initConfig({
        less: {
            development: {
                options: {
                    paths: ["www/css"],
                    yuicompress: true
                },
                files: {
                    "www/css/screen.css": "www/css/screen.less"
                }
            }
        },
        watch: {
            files: "www/css/*.less",
            tasks: ["less"]
        }
    });
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', 'watch'); // registrace defaultní úlohy
};