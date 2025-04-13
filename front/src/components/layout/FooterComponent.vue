<script setup lang="ts">
import tokens from '@/i18n/tokens';

const links = {
    games: {
        title: tokens.footer.links.games.title,
        items: {
            [tokens.footer.links.games.supportedList]: '#',
            [tokens.footer.links.games.suggestion]: '#',
        },
    },
    documentation: {
        title: tokens.footer.links.documentation.title,
        items: {
            [tokens.footer.links.documentation.createRoom]: '#',
            [tokens.footer.links.documentation.connectGame]: '#',
            [tokens.footer.links.documentation.connectTchat]: '#',
        },
    },
    currentGame: {
        title: tokens.footer.links.currentGames.title,
        items: {},
    },
};
</script>

<template>
    <footer>
        <div class="footer__content">
            <div class="links">
                <div v-for="(block, key) in links" :key="key">
                    <h3>{{ $t(block.title) }}</h3>
                    <ul>
                        <li v-for="(link, name) in block.items" :key="name">
                            <a :href="link">{{ $t(name) }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="support-us">
                <h3>{{ $t(tokens.footer.support.title) }}</h3>
                <ul>
                    <li>
                        <a target="_blank" href="https://github.com/Fan2Shrek/danin">
                            <img src="@/assets/various/github-mark.svg" alt="Github" />
                        </a>
                    </li>
                    <li><img src="@/assets/various/paypal.svg" alt="Paypal" /></li>
                </ul>
            </div>
        </div>
        <div class="bottom">
            <p>{{ $t(tokens.footer.copyright) }}</p>
            <p>© Danin 2025</p>
        </div>
    </footer>
</template>

<style scoped lang="scss">
footer {
    color: black;
    width: 100%;
    height: 250px;
    background-color: #f8f9fa;
    border: 1px solid black;
    padding: 0;

    .footer__content {
        display: flex;

        .links {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            margin: 0 auto;
            max-width: 1200px;
            width: 75%;

            h3 {
                font-size: 1.5rem;
                margin-bottom: 10px;
            }

            ul {
                list-style-type: none;
                padding: 0;

                li {
                    margin-bottom: 5px;

                    a {
                        text-decoration: none;
                        color: black;

                        &:hover {
                            text-decoration: underline;
                        }
                    }
                }
            }
        }

        .support-us {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
            margin: 0 auto;
            max-width: 1200px;

            h3 {
                font-size: 1.5rem;
                margin-bottom: 10px;
            }

            ul {
                list-style-type: none;
                padding: 0;
                display: flex;
                gap: 20px;

                li {
                    margin-bottom: 5px;

                    img {
                        width: 30px;
                        height: 30px;
                    }
                }
            }
        }
    }

    .bottom {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        margin: 0 auto;
        max-width: 1200px;
    }
}
</style>
