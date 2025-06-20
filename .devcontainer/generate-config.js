import * as os from 'node:os'
import fs from 'fs'
import { execSync } from 'node:child_process'

const platform = os.platform()
const mounts = [
    "source=${localWorkspaceFolder}/.devcontainer/php/php.ini,target=/usr/local/etc/php/conf.d/custom.ini,type=bind",
]

if (platform === 'win32') {
    mounts.push(
        'source=${localEnv:USERPROFILE}\\.gitconfig,target=/root/.gitconfig,type=bind,ro',
        'source=${localEnv:USERPROFILE}\\gitconfigs,target=/root/gitconfigs,type=bind,ro',
        'source=${localEnv:USERPROFILE}\\.gnupg,target=/root/.gnupg,type=bind,ro',
    )
} else {
    mounts.push(
        'source=${localEnv:HOME}/.gitconfig,target=/root/.gitconfig,type=bind,ro',
        'source=${localEnv:HOME}/gitconfigs,target=/root/gitconfigs,type=bind,ro',
        'source=${localEnv:HOME}/.gnupg,target=/root/.gnupg,type=bind,ro'
    )
    if (platform === 'linux') {
        const userId = execSync('id -u').toString().trim()
        mounts.push(
            `source=/run/user/${userId}/gnupg/S.gpg-agent,target=/root/.gnupg/S.gpg-agent,type=bind,ro`,
            `source=/run/user/${userId}/gnupg/S.gpg-agent.extra,target=/root/.gnupg/S.gpg-agent.extra,type=bind,ro`,
            `source=/run/user/${userId}/gnupg/S.gpg-agent.ssh,target=/root/.gnupg/S.gpg-agent.ssh,type=bind,ro`,
        )
    }

}

const config = {
    name: 'WP Food spot plugin vendor',
    build: {
        dockerfile: "Dockerfile"
    },
    customizations: {
        jetbrains: {
            backend: 'PhpStorm',
            plugins: [
            ],
        },
    },
    remoteUser: "root",
    postCreateCommand: "composer install",
    mounts,
}

fs.writeFileSync('devcontainer.json', JSON.stringify(config, null, 4))

console.log(JSON.stringify(config, null, 4))
