// Cadangan jalur CI Jenkins — jalur deploy utama adalah Dokploy (build dari
// Dockerfile repo ini). Pakai file ini hanya jika pipeline Jenkins temanmu
// yang menjadi pintu deploy. Masih perlu disepakati dengan pemilik Jenkins:
// akses docker daemon untuk agent, registry tujuan push image, dan apakah
// Dokploy di-trigger via webhook/API setelah push.
pipeline {
    agent any

    environment {
        IMAGE = "qurban-pyramid:${env.BUILD_NUMBER}"
    }

    stages {
        stage('Checkout') {
            steps { checkout scm }
        }

        stage('Build image') {
            steps { sh 'docker build -t $IMAGE .' }
        }

        stage('Test') {
            steps {
                sh '''
                    docker run --rm \
                        -e APP_ENV=testing -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: \
                        $IMAGE sh -c "php artisan test"
                '''
            }
        }

        stage('Deploy') {
            steps {
                // TBD dengan pemilik VPS: push ke registry lalu trigger Dokploy,
                // atau docker compose up langsung di host.
                sh 'echo "deploy step belum dikonfigurasi"'
            }
        }
    }
}
