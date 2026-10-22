import type { Metadata } from "next";
import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";
import Providers from "./providers";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  title: "Danin",
  description: "Danin : découvrez une nouvelle façon de jouer avec vos amis",
  openGraph: {
    title: "Danin",
    description: "Danin : découvrez une nouvelle façon de jouer avec vos amis",
    url: "https://sruuua.fr",
    siteName: "Danin",
    images: ["https://sruuua.fr/D.png"],
    locale: "fr_FR",
    type: "website",
  },
};

export default function RootLayout({ children }: LayoutProps<"/">) {
  return (
    <html
      lang="fr"
      className={`${geistSans.variable} ${geistMono.variable} h-full antialiased`}
    >
      <body className="flex min-h-full flex-col bg-white dark:bg-slate-950">
        <Providers>{children}</Providers>
      </body>
    </html>
  );
}
