# Course Checker Info [![Moodle Plugin CI](https://github.com/ffhs/moodle-block_course_checker_info/actions/workflows/moodle-plugin-ci.yml/badge.svg?branch=MOODLE_404_STABLE)](https://github.com/ffhs/moodle-block_course_checker_info/actions/workflows/moodle-plugin-ci.yml)

This Moodle block plugin displays the current status of course checks performed by the `local_course_checker` plugin. It provides quick access to the latest results, timestamps, and rerun options directly from the course page.

---

## 🧰 Features

- Displays timestamp of last checker run.
- Shows percentage breakdown of check result statuses (`successful`, `warning`, `failed`, `error`).
- Indicates whether a check run is currently in progress.

---

## 🧩 Dependencies

This plugin requires the following to function properly:

- <code>local_course_checker</code> — must be installed and operational.

---

## 📁Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to **Site administration > Plugins > Install plugins**.
2. Upload the ZIP file with the plugin code. You should only be prompted to add extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## 📁Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/blocks/course_checker_info

Afterwards, log in to your Moodle site as an admin and go to **Site administration > Notifications** to complete the installation.

Alternatively, you can run

```bash
php admin/cli/upgrade.php
```

to complete the installation from the command line.

## 📦 Installing via GitHub

Clone the plugin into your Moodle instance:

```bash
cd /path/to/moodle
git clone https://github.com/ffhs/moodle-block_course_checker_info.git blocks/course_checker_info
```

Run the upgrade script:

```bash
php admin/cli/upgrade.php
```

Or complete the installation via the Moodle web interface: **Site administration > Notifications**

---

## 🚀 Usage

Once installed:

1. Go to a course page.
2. Add the block via *"Add a block" → "Course Checker Info"*.
3. The block will display the latest checker results and options.

---

## 🧠 Authors

**Simon Gisler**\
[simon.gisler@ffhs.ch](mailto:simon.gisler@ffhs.ch)\
<a href="https://www.ffhs.ch" target="_blank">Swiss Distance University of Applied Sciences (FFHS)</a>

**Stefan Dani**\
[stefan.dani@ffhs.ch](mailto:stefan.dani@ffhs.ch)\
<a href="https://www.ffhs.ch" target="_blank">Swiss Distance University of Applied Sciences (FFHS)</a>

---

## 📝 License

This plugin is licensed under the [GNU GPL v3](https://www.gnu.org/licenses/gpl-3.0.html).