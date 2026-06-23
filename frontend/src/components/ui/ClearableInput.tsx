import { forwardRef, useRef, type ReactNode, type InputHTMLAttributes, type MutableRefObject } from 'react'
import { X } from 'lucide-react'
import { cn } from '@/lib/utils'

interface ClearableInputProps extends Omit<InputHTMLAttributes<HTMLInputElement>, 'onChange' | 'value'> {
  value: string
  onChange: (value: string) => void
  trailingExtra?: ReactNode
  wrapperClassName?: string
}

export const ClearableInput = forwardRef<HTMLInputElement, ClearableInputProps>(
  ({ value, onChange, trailingExtra, wrapperClassName, className, ...props }, ref) => {
    const innerRef = useRef<HTMLInputElement>(null)

    function setRefs(node: HTMLInputElement | null) {
      innerRef.current = node
      if (typeof ref === 'function') ref(node)
      else if (ref) (ref as MutableRefObject<HTMLInputElement | null>).current = node
    }

    function handleClear() {
      onChange('')
      innerRef.current?.focus()
    }

    return (
      <div className={cn('relative', wrapperClassName)}>
        <input
          ref={setRefs}
          value={value}
          onChange={e => onChange(e.target.value)}
          className={cn(className, trailingExtra ? 'pr-16' : 'pr-9')}
          {...props}
        />
        {value && (
          <button
            type="button"
            onClick={handleClear}
            tabIndex={-1}
            aria-label="Limpar campo"
            className={cn(
              'absolute top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors',
              trailingExtra ? 'right-9' : 'right-3',
            )}
          >
            <X className="w-4 h-4" />
          </button>
        )}
        {trailingExtra && (
          <div className="absolute right-3 top-1/2 -translate-y-1/2">
            {trailingExtra}
          </div>
        )}
      </div>
    )
  },
)
ClearableInput.displayName = 'ClearableInput'
