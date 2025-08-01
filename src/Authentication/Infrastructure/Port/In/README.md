# Authentication Context - Architecture des Ports & Adapters

## Vue d'ensemble des flux

```mermaid
graph TB
    subgraph "External Contexts"
        ACC[Account Context]
        IDT[Identity Context]
        ADM[Admin Tools]
    end
    
    subgraph "Authentication Context"
        subgraph "Ports IN"
            ASP[AuthenticationServicePort]
            CMP[CredentialManagementPort]
        end
        
        subgraph "Adapters IN"
            ASA[AuthenticationServiceAdapter]
            CMA[CredentialManagementAdapter]
        end
        
        subgraph "Domain & Application"
            CMD[Command Handlers]
            DOM[Domain Services]
            QRY[Query Handlers]
        end
        
        subgraph "Adapters OUT"
            ACA[AccountContextAdapter]
            ICA[IdentityContextAdapter]
            NCA[NotificationContextAdapter]
        end
        
        subgraph "Ports OUT"
            ACP[AccountContextPort]
            ICP[IdentityContextPort]
            NCP[NotificationContextPort]
        end
    end
    
    ACC -->|Creates Credentials| ASP
    ACC -->|Revokes Credentials| ASP
    IDT -->|Identity Changes| ASP
    ADM -->|Manage Credentials| CMP
    
    ASP --> ASA
    CMP --> CMA
    
    ASA --> CMD
    CMA --> QRY
    
    DOM --> ACP
    DOM --> ICP
    DOM --> NCP
    
    ACP --> ACA
    ICP --> ICA
    NCP --> NCA
    
    ACA -->|Check Status| ACC
    ICA -->|Resolve IDs| IDT
```

## Flux détaillé : Création de Magic Link

```
sequenceDiagram
    participant AC as Account Context
    participant ASP as AuthenticationServicePort IN
    participant ASA as AuthenticationServiceAdapter IN
    participant CH as CreateMagicLinkHandler
    participant ACP as AccountContextPort OUT
    participant ACA as AccountContextAdapter OUT
    participant ACC as Account Context (ACL)
    
    AC->>ASP: createMagicLinkCredentials(userId, email)
    ASP->>ASA: Implement interface
    ASA->>ASA: Convert strings to VOs
    ASA->>CH: dispatch(CreateMagicLinkCommand)
    CH->>ACP: isAccountActive(userId)
    ACP->>ACA: Implement interface
    ACA->>ACC: getAccountStatus(UserId)
    ACC-->>ACA: AccountStatusDTO
    ACA-->>ACP: boolean
    ACP-->>CH: true
    CH->>CH: Create MagicLink
    CH->>CH: Send email
    CH-->>ASA: Success
    ASA-->>ASP: void
    ASP-->>AC: Complete
```
