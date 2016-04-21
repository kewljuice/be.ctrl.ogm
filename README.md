# CiviCRM extension: OGM

OGM: Add "gestructureerde mededeling" for CiviCRM contributions
- [Installation](#installation)
- [Usage](#usage)

***

## Installation

- You can directly clone to your CiviCRM extension directory using<br>
```$ git clone https://github.com/kewljuice/be.ctrl.ogm.git```

- You can also download a zip file, and extract in your extension directory<br>
```$ git clone https://github.com/kewljuice/be.ctrl.ogm/archive/master.zip```

- Configure CiviCRM Extensions Directory which can be done from<br>
```"Administer -> System Settings -> Directories".```

- Configure Extension Resource URL which can be done from<br>
```"Administer -> System Settings -> Resource URLs".```

- The next step is enabling the extension which can be done from<br> 
```"Administer -> System Settings -> Manage CiviCRM Extensions".```

## Usage

- You can use following tokens with 'events':
    - [token_ogm]
    - [token_email]
    - [token_date]
    - [token_amount]

- You can use following tokens with 'memberships':
    - [token_ogm]
    - [token_email]
    - [token_date]
    - [token_amount]
    - [token_membership]