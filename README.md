Absolutely! Here's a **professional, ready-to-use README** for your “Say It Nicer” Telex agent project. You can drop this in your repo or documentation.

# Say It Nicer — Agent 2 Agent

## Overview

**Say It Nicer** is an AI-powered agent built to help users communicate more politely and professionally. The agent detects harsh, blunt, or rude messages and rephrases them into a kind, concise, and professional tone. If a message is already polite, it simply returns it with a friendly acknowledgment.

This agent is built with **Laravel** and **Gemini**, and fully integrated with **Telex.im** via a public API endpoint.

---

## Features

* **Tone Detection:** Analyzes incoming messages for harsh or blunt language.
* **Message Rewriting:** Rewrites messages to be polite and professional without changing meaning.
* **Politeness Feedback:** If a message is already kind, responds with a friendly acknowledgment (e.g., “That already sounds nice!”).
* **Telex Integration:** Works directly in Telex channels using the workflow builder.

---

## Workflow Details (Telex)

* **Agent Name:** `SayItNicer`
* **Category:** `communication`
* **Short Description:** Polishes messages into a kind and professional tone.
* **Long Description:** Say It Nicer is an AI agent that rewrites messages to be polite, professional, and friendly. It detects harsh or blunt tone and gently rephrases it while keeping the original meaning. If the message is already polite, it returns it with a friendly acknowledgment. Built using Laravel and Gemini.
* **URL (Base):** `https://api.techtrovelab.com`

---

## Installation / Setup

1. **Clone the repo**:

   ```bash
   git clone https://github.com/integral-hub/backend-api.git
   cd backend-api
   ```

2. **Install dependencies**:

   ```bash
   composer install
   ```

3. **Create environment file**:

   ```bash
   cp .env.example .env
   ```

   Set your **Gemini API key**:

   ```
   Gemini_API_KEY=your_Gemini_key
   ```

## Live API Endpoint

**POST** `https://api.techtrovelab.com/api/agent`

**GET** `https://api.techtrovelab.com/api/.well-known/agent.json`

---

## Testing

1. Set up the Telex workflow and paste the public API URL in the **Publish → How It Works** field.
2. Send a message in your Telex channel.
3. Confirm that your agent:

   * Rewrites harsh/blunt messages politely.
   * Leaves already polite messages unchanged and responds with acknowledgment.

---

## Tech Stack

* **Backend:** Laravel (PHP)
* **AI:** Gemini GPT API
* **Integration:** Telex.im Workflow (A2A Agent Node)

---

