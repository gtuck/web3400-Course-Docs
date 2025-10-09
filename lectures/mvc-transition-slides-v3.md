---
theme: default
title: From PRG to MVC
info: "Bridge lecture between Project 01 (PRG Pattern) and Project 02 (Building a PHP MVC Framework)"
layout: cover
addons:
  - slidev-addon-toc
paginate: true
---

# From PRG to MVC  
### Evolving from Spaghetti PHP to Structured Design

<!--
🗣️ Speaker Notes:
This lecture connects what students have just built — their CRUD project using the PRG pattern — to where we’re going next: building an MVC framework from scratch.
The goal is to show that MVC isn’t something new to fear; it’s simply a cleaner, more structured version of what they’re already doing.
-->

---

## Where We’ve Been — The PRG Pattern

**PRG = Post → Redirect → Get**

```text
Form → POST → Process → Redirect → GET → Render
```

✅ Prevents duplicate submissions  
✅ Improves user experience  
✅ Keeps the browser’s back button safe  

---

## The PRG Flow (Visually)

<div class="grid place-items-center">
<svg width="900" height="360" viewBox="0 0 900 360" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
      <polygon points="0 0, 10 3.5, 0 7" fill="#6B7280"/>
    </marker>
    <filter id="shadow"><feDropShadow dx="0" dy="2" stdDeviation="4" flood-opacity="0.15"/></filter>
    <style>
      .box { rx: 14; ry: 14; stroke: #1a1a1a; stroke-opacity: .1; filter: url(#shadow);}
      .label { font: 600 16px/1.2 ui-sans-serif; fill: #111827; }
      .sub { font: 12px ui-sans-serif; fill: #374151; }
      .arrow { stroke: #6B7280; stroke-width: 2.5; marker-end: url(#arrowhead); fill:none; }
    </style>
  </defs>

  <rect x="40" y="140" width="140" height="80" class="box" fill="#E5E7EB"/>
  <text x="110" y="175" text-anchor="middle" class="label">Form</text>
  <text x="110" y="195" text-anchor="middle" class="sub">User Input</text>

  <rect x="260" y="40" width="180" height="80" class="box" fill="#DBEAFE"/>
  <text x="350" y="75" text-anchor="middle" class="label">POST</text>

  <rect x="260" y="240" width="180" height="80" class="box" fill="#D1FAE5"/>
  <text x="350" y="275" text-anchor="middle" class="label">GET</text>

  <rect x="500" y="140" width="180" height="80" class="box" fill="#FFEDD5"/>
  <text x="590" y="175" text-anchor="middle" class="label">Render Page</text>

  <path d="M180 180 L260 80" class="arrow"/>
  <path d="M440 80 L590 140" class="arrow"/>
  <path d="M590 220 L440 280" class="arrow"/>
  <path d="M260 280 L180 180" class="arrow"/>

</svg>
</div>

---

## Bridge Summary: PRG → MVC

PRG taught us how to handle user actions safely.  
Now we’ll evolve it into a reusable structure.

| PRG Concept | MVC Upgrade | Why It Matters |
|--------------|--------------|----------------|
| Process form & redirect | Controller | Isolates logic and routing |
| Database logic inline | Model | Centralizes and reuses data access |
| HTML mixed with PHP | View | Keeps presentation separate |
| One file per feature | Modular folders | Easier to extend and maintain |

💡 **MVC formalizes what PRG started** — structured, scalable, and clean.

---

## Morphing the Flow — PRG → MVC

<transition name="scale">
<div class="grid place-items-center">
  <img src="https://via.placeholder.com/800x420?text=Transition:+PRG+to+MVC" alt="Transition Diagram">
</div>
</transition>

💬 The PRG cycle (POST–Redirect–GET) becomes the MVC request–response cycle with roles (Controller–Model–View).

---

## Mapping PRG → MVC

| PRG Step | MVC Role | Example in Project 01 |
|-----------|-----------|-----------------------|
| **POST → Process** | **Controller** | The code that handled `$_POST`, validated input, and decided what to do next |
| **Database Query / Insert** | **Model** | Your PDO `INSERT` or `SELECT` queries inside the same file |
| **Redirect (header)** | **Controller → View** | Controller chooses which page (view) to display or redirect to |
| **GET → Render Page** | **View** | The final HTML mixed with PHP output |
| **Form Submission / Input** | **User Action → Controller** | What triggers the MVC request cycle |

💡 *MVC doesn’t replace PRG — it formalizes it.*

---

## File Structure: Then vs Now

<div class="grid grid-cols-2 gap-8">

<div>

### Project 01 (PRG)
```text
/project-01
  blog_create.php
  blog_edit.php
  blog_delete.php
  blog_post.php
  contact.php
  /templates
    head.php
    nav.php
    footer.php
  /sql
    schema.sql
```

</div>

<div>

### Project 02 (MVC)
```text
/project-02
  /app
    /controllers
      PostController.php
      ContactController.php
    /models
      Post.php
      Contact.php
    /views
      /posts
        create.php
        edit.php
      /contact
        index.php
  /public
    index.php (router)
```

</div>

</div>

💡 *Separation creates clarity and reusability*

---

## Full MVC Flow

```text
User → Controller → Model → Controller → View → Response
```

<div class="grid place-items-center">
  <img src="https://via.placeholder.com/900x420?text=MVC+Flow+Diagram" alt="MVC Flow Diagram">
</div>

---

## Success Checklist

Before leaving today, make sure you can:

- [ ] Explain what each MVC layer does in one sentence
- [ ] Identify which layer handles database queries
- [ ] Identify which layer handles user input
- [ ] Identify which layer handles HTML output
- [ ] Sketch the MVC flow from memory
- [ ] Explain why separation of concerns matters
- [ ] Mark up one of your files with colored layers

💡 **If you can’t check all boxes, ask now!**

---

layout: center
---

# Let’s Build Something Better 🚀

*MVC isn’t just a pattern—it’s a mindset*

**See you next class with your marked-up code!**
