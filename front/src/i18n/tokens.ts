const tokens = {
    login: {
        title: 'login.title',
        email: 'login.email',
        password: 'login.password',
        submit: 'login.submit',
        register: {
            link: 'login.register.link',
            cta: 'login.register.cta',
        },
        totp: {
            title: 'login.totp.title',
            submit: 'login.totp.submit',
            input: 'login.totp.input',
            error: 'login.totp.error',
        },
        error: {
            invalid: 'login.error.invalid',
            empty: 'login.error.empty',
        },
    },
    home: {
        title: 'home.title',
        subtitle: 'home.description',
        cta: 'home.cta',
        games: {
            title: 'home.games.title',
        },
        providers: {
            title: 'home.providers.title',
        },
        events: {
            title: 'home.events.title',
        },
    },
    user: {
        username: 'user.username',
        email: 'user.email',
        password: 'user.password',
        constraints: {
            password: {
                minLength: 'user.password.minLength',
                uppercase: 'user.password.uppercase',
                lowercase: 'user.password.lowercase',
                number: 'user.password.number',
                special: 'user.password.special',
                strength: {
                    veryWeak: 'user.password.strength.veryWeak',
                    weak: 'user.password.strength.weak',
                    medium: 'user.password.strength.medium',
                    strong: 'user.password.strength.strong',
                    excellent: 'user.password.strength.excellent',
                },
            },
        },
    },
    games: {
        title: 'games.title',
        subtitle: 'games.subtitle',
    },
    room: {
        create: {
            game: {
                title: 'room.create.game.title',
                howTo: 'room.create.game.howTo',
                cta: 'room.create.game.cta',
            },
            settings: {
                title: 'room.create.settings.title',
                transport: 'room.create.settings.transport',
                host: 'room.create.settings.host',
                port: 'room.create.settings.port',
            },
            commands: {
                title: 'room.create.commands.title',
            },
            providers: {
                title: 'room.create.providers.title',
            },
            submit: 'room.create.submit',
        },
        start: {
            title: 'room.start.title',
            description: 'room.start.description',
            error: 'room.start.error',
            requestConnectionToGame: 'room.start.requestConnectionToGame',
            localSetupError: 'room.start.localSetupError',
            providers: 'room.start.providers',
            providerCommand: 'room.start.providerCommand',
            copyToken: 'room.start.copyToken',
            go: 'room.start.go',
        },
        tchat: {
            title: 'room.tchat.title',
            send: 'room.tchat.send',
            placeholder: 'room.tchat.placeholder',
            commands: {
                title: 'room.tchat.commands.title',
                description: 'room.tchat.commands.description',
            },
        },
    },
    totp: {
        enable: 'totp.enable',
        description: 'totp.description',
    },
    update: {
        title: 'update.title',
        subtitle: 'update.subtitle',
        submit: 'update.submit',
        success: 'update.success',
    },
    register: {
        title: 'register.title',
        subtitle: 'register.subtitle',
        success: 'register.success',
        confirmPassword: 'user.confirmPassword',
        submit: 'register.submit',
        submitting: 'register.submitting',
        placeholder: {
            username: 'register.placeholder.username',
            email: 'register.placeholder.email',
            password: 'register.placeholder.password',
            confirmPassword: 'register.placeholder.confirmPassword',
        },
        error: {
            miscellaneous: 'register.error.miscellaneous',
            username: {
                alreadyExists: 'register.error.username.alreadyExists',
                minLength: 'register.error.username.minLength',
                maxLength: 'register.error.username.maxLength',
                regex: 'register.error.username.regex',
            },
            email: {
                alreadyExists: 'register.error.email.alreadyExists',
                invalid: 'register.error.email.invalid',
            },
            password: {
                minLength: 'register.error.password.minLength',
                notStrong: 'register.error.password.notStrong',
            },
            confirmPassword: {
                mismatch: 'register.error.confirmPassword.mismatch',
            },
        },
    },
    navbar: {
        links: {
            games: 'navbar.links.games',
            tchat: 'navbar.links.tchat',
            createRoom: 'navbar.links.createRoom',
            profile: 'navbar.links.profile',
            logout: 'navbar.links.logout',
            login: 'navbar.links.login',
        },
    },
    footer: {
        copyright: 'footer.copyright',
        links: {
            games: {
                title: 'footer.links.games.title',
                supportedList: 'footer.links.games.supportedList',
                suggestion: 'footer.links.games.suggestion',
            },
            documentation: {
                title: 'footer.links.documentation.title',
                createRoom: 'footer.links.documentation.createRoom',
                connectGame: 'footer.links.documentation.connectGame',
                connectTchat: 'footer.links.documentation.connectTchat',
            },
            currentGames: {
                title: 'footer.links.currentGames.title',
            },
        },
        support: {
            title: 'footer.support.title',
        },
    },
};

export default tokens;
