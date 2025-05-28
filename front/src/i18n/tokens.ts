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
    },
    room: {
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
    navbar: {
        links: {
            games: 'navbar.links.games',
            tchat: 'navbar.links.tchat',
            createRoom: 'navbar.links.createRoom',
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
